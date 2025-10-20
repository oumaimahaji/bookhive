#!/usr/bin/env python3
"""
Service IA pour la recommandation de catégorie et génération de description
"""

import sys
import json
import re
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import cosine_similarity
import numpy as np
import mysql.connector

class BookAIRecommender:
    def __init__(self, db_config):
        self.db_config = db_config
        self.vectorizer = TfidfVectorizer(stop_words='english', max_features=1000)
        self.is_trained = False
        
    def get_categories_from_db(self):
        """Récupère toutes les catégories de la base de données"""
        try:
            conn = mysql.connector.connect(**self.db_config)
            cursor = conn.cursor(dictionary=True)
            
            cursor.execute("SELECT id, nom, description FROM categories")
            categories = cursor.fetchall()
            
            cursor.close()
            conn.close()
            
            return categories
        except Exception as e:
            print(f"Erreur récupération catégories: {str(e)}", file=sys.stderr)
            return []
    
    def get_existing_books_for_training(self):
        """Récupère les livres existants pour l'entraînement du modèle"""
        try:
            conn = mysql.connector.connect(**self.db_config)
            cursor = conn.cursor(dictionary=True)
            
            cursor.execute("""
                SELECT b.titre, b.description, c.nom as category_name, c.id as category_id 
                FROM books b 
                JOIN categories c ON b.category_id = c.id 
                WHERE b.titre IS NOT NULL AND b.titre != ''
            """)
            books = cursor.fetchall()
            
            cursor.close()
            conn.close()
            
            return books
        except Exception as e:
            print(f"Erreur récupération livres: {str(e)}", file=sys.stderr)
            return []
    
    def train_category_model(self):
        """Entraîne le modèle de recommandation de catégorie"""
        books = self.get_existing_books_for_training()
        categories = self.get_categories_from_db()
        
        if not books or not categories:
            return categories
        
        # Préparation des données d'entraînement
        documents = []
        category_labels = []
        
        for book in books:
            # Combiner titre et description pour meilleur contexte
            text = f"{book['titre']} {book['description'] or ''}"
            documents.append(text)
            category_labels.append(book['category_id'])
        
        # Entraînement du modèle TF-IDF
        if documents:
            try:
                tfidf_matrix = self.vectorizer.fit_transform(documents)
                self.tfidf_matrix = tfidf_matrix
                self.category_labels = category_labels
                self.is_trained = True
            except Exception as e:
                print(f"Erreur entraînement: {str(e)}", file=sys.stderr)
                self.is_trained = False
        else:
            self.is_trained = False
            
        return categories
    
    def recommend_category(self, book_title, book_author=""):
        """Recommande une catégorie basée sur le titre et l'auteur"""
        try:
            categories = self.train_category_model()
            
            if not categories:
                print("❌ Aucune catégorie trouvée dans la base de données")
                return None
            
            if not self.is_trained:
                # Si pas d'entraînement possible, retourner la catégorie la plus utilisée
                return self.get_most_common_category()
            
            # Préparation du texte d'entrée
            input_text = f"{book_title} {book_author}"
            
            # Transformation avec le vectorizer entraîné
            input_tfidf = self.vectorizer.transform([input_text])
            
            # Calcul de similarité avec tous les documents
            similarities = cosine_similarity(input_tfidf, self.tfidf_matrix)
            
            # Trouver les livres les plus similaires
            most_similar_idx = np.argmax(similarities[0])
            best_match_category_id = self.category_labels[most_similar_idx]
            
            # Trouver le nom de la catégorie
            recommended_category = next(
                (cat for cat in categories if cat['id'] == best_match_category_id), 
                None
            )
            
            if not recommended_category:
                # Si aucune catégorie trouvée, utiliser la plus commune
                recommended_category = self.get_most_common_category()
            
            return recommended_category
            
        except Exception as e:
            print(f"Erreur recommandation: {str(e)}", file=sys.stderr)
            return self.get_most_common_category()
    
    def get_most_common_category(self):
        """Récupère la catégorie la plus utilisée"""
        try:
            conn = mysql.connector.connect(**self.db_config)
            cursor = conn.cursor(dictionary=True)
            
            cursor.execute("""
                SELECT c.id, c.nom, c.description, COUNT(b.id) as book_count
                FROM categories c 
                LEFT JOIN books b ON c.id = b.category_id 
                GROUP BY c.id, c.nom, c.description 
                ORDER BY book_count DESC 
                LIMIT 1
            """)
            
            result = cursor.fetchone()
            cursor.close()
            conn.close()
            
            if result:
                return result
            else:
                # Si aucune catégorie n'existe, retourner None
                return None
                
        except Exception as e:
            print(f"Erreur catégorie commune: {str(e)}", file=sys.stderr)
            return None
    
    def generate_description(self, book_title, book_author, category_name=None):
        """Génère une description de livre en français"""
        
        # Si pas de catégorie, générer une description générale
        if not category_name:
            category_name = "littérature"
        
        # Modèles de description en français
        templates = [
            f"'{book_title}' de {book_author} est un livre captivant qui vous transportera dans un voyage inoubliable.",
            f"Découvrez '{book_title}' de {book_author}, un remarquable roman rempli de personnages fascinants et d'une narration engageante.",
            f"Dans '{book_title}', l'auteur {book_author} explore des thèmes passionnants dans ce chef-d'œuvre qui laissera les lecteurs subjugués.",
            f"Vivez la magie de '{book_title}' de {book_author}, un livre qui allie écriture brillante et moments inoubliables.",
            f"'{book_title}' est une œuvre par {book_author} qui met en valeur la voix unique et le talent de conteur de l'auteur."
        ]
        
        # Améliorations basées sur le titre
        title_lower = book_title.lower()
        
        if any(word in title_lower for word in ['mystère', 'secret', 'ombre', 'sombre', 'énigme']):
            description = f"'{book_title}' de {book_author} est un roman palpitant rempli de suspense et de rebondissements inattendus qui vous tiendront en haleine jusqu'à la dernière page."
        elif any(word in title_lower for word in ['amour', 'cœur', 'romance', 'passion']):
            description = f"Un roman touchant, '{book_title}' de {book_author} explore les complexités des relations et la puissance des connections humaines dans un récit émouvant."
        elif any(word in title_lower for word in ['aventure', 'voyage', 'quête', 'expédition']):
            description = f"Rejoignez l'aventure épique dans '{book_title}' de {book_author}, un récit passionnant qui emmène les lecteurs dans un extraordinaire voyage à travers des terres inconnues."
        elif any(word in title_lower for word in ['science', 'futur', 'technologie', 'espace']):
            description = f"'{book_title}' de {book_author} est une réflexion stimulante qui explore les frontières de la science et de l'imagination, remettant en question notre perception de la réalité."
        else:
            description = templates[np.random.randint(0, len(templates))]
        
        return description

# === SERVICE WEB POUR VOTRE FORMULAIRE ===
from flask import Flask, request, jsonify
from flask_cors import CORS

# Création du service web
app = Flask(__name__)
CORS(app)

# Configuration de la base de données
db_config = {
    'host': '127.0.0.1',
    'user': 'root',
    'password': '',
    'database': 'bookshare'
}

@app.route('/analyze-book', methods=['POST'])
def analyze_book_web():
    """Version web pour votre formulaire"""
    try:
        # Récupérer les données du formulaire avec get_json() pour plus de fiabilité
        data = request.get_json()
        
        if not data:
            return jsonify({'success': False, 'error': 'Données JSON manquantes'})
            
        book_title = data.get('title', '')
        book_author = data.get('author', '')
        
        print(f"📖 Analyse demandée pour: {book_title} par {book_author}")
        
        if not book_title:
            return jsonify({'success': False, 'error': 'Titre manquant'})
        
        # Utiliser l'IA existante
        recommender = BookAIRecommender(db_config)
        recommended_category = recommender.recommend_category(book_title, book_author)
        
        # Vérifier si une catégorie a été trouvée
        if recommended_category and isinstance(recommended_category, dict) and 'nom' in recommended_category:
            category_name = recommended_category['nom']
            category_id = recommended_category.get('id')
            category_description = recommended_category.get('description', 'Catégorie recommandée par IA')
        else:
            # Aucune catégorie trouvée - ne pas suggérer de catégorie
            category_name = None
            category_id = None
            category_description = None
        
        description = recommender.generate_description(book_title, book_author, category_name)
        
        if category_name:
            print(f"✅ IA suggère: {category_name}")
        else:
            print("ℹ️  Aucune catégorie suggérée - base de données vide ou pas de correspondance")
        
        # Réponse pour le formulaire
        response_data = {
            'success': True,
            'generated_description': description
        }
        
        # Ajouter la catégorie seulement si elle existe
        if category_id and category_name:
            response_data['recommended_category'] = {
                'id': category_id,
                'nom': category_name,
                'description': category_description
            }
        
        return jsonify(response_data)
        
    except Exception as e:
        print(f"❌ Erreur: {e}")
        return jsonify({'success': False, 'error': str(e)})

@app.route('/health', methods=['GET'])
def health_check():
    """Vérification que le service fonctionne"""
    return jsonify({'status': 'OK', 'message': 'Service IA actif'})

def run_web_service():
    """Lance le service web"""
    print("🤖 SERVICE IA DÉMARRÉ!")
    print("📍 Adresse: http://127.0.0.1:5000")
    print("🔍 Test: http://127.0.0.1:5000/health")
    print("⏹️  Pour arrêter: Appuyez sur Ctrl+C")
    app.run(host='127.0.0.1', port=5000, debug=True)

# Mode service web uniquement
if __name__ == '__main__':
    run_web_service()