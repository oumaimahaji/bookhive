from flask import Flask, request, jsonify
import pymysql
import os
from flask_cors import CORS

app = Flask(__name__)
CORS(app)  # Enable CORS for all routes

# Database configuration - same as Laravel
DB_CONFIG = {
    'host': '127.0.0.1',
    'port': 3306,
    'user': 'root',
    'password': '',  # Empty password as per .env file
    'database': 'bookshare',
    'charset': 'utf8mb4',
    'cursorclass': pymysql.cursors.DictCursor
}

def get_db_connection():
    """Create and return database connection"""
    return pymysql.connect(**DB_CONFIG)

def get_books_from_db():
    """Get books from database"""
    connection = None
    try:
        connection = get_db_connection()
        with connection.cursor() as cursor:
            # Query to get valid books with their categories
            sql = """
                SELECT b.*, c.nom as category_name
                FROM books b
                LEFT JOIN categories c ON b.category_id = c.id
                WHERE b.is_valid = 1
                ORDER BY b.created_at DESC
                LIMIT 20
            """
            cursor.execute(sql)
            books = cursor.fetchall()
            return books
    except Exception as e:
        print(f"Database error: {e}")
        return []
    finally:
        if connection:
            connection.close()

def search_books_in_db(query):
    """Search books by title or author"""
    connection = None
    try:
        connection = get_db_connection()
        with connection.cursor() as cursor:
            sql = """
                SELECT b.*, c.nom as category_name
                FROM books b
                LEFT JOIN categories c ON b.category_id = c.id
                WHERE b.is_valid = 1
                AND (b.titre LIKE %s OR b.auteur LIKE %s)
                ORDER BY b.created_at DESC
                LIMIT 5
            """
            search_term = f"%{query}%"
            cursor.execute(sql, (search_term, search_term))
            books = cursor.fetchall()
            return books
    except Exception as e:
        print(f"Database search error: {e}")
        return []
    finally:
        if connection:
            connection.close()

def get_categories_from_db():
    """Get all categories from database"""
    connection = None
    try:
        connection = get_db_connection()
        with connection.cursor() as cursor:
            sql = "SELECT * FROM categories ORDER BY nom"
            cursor.execute(sql)
            categories = cursor.fetchall()
            return categories
    except Exception as e:
        print(f"Database categories error: {e}")
        return []
    finally:
        if connection:
            connection.close()

@app.route("/chat", methods=["POST"])
def chat():
    data = request.get_json()
    user_message = data.get("message", "").lower()

    try:
        # Handle book availability queries
        if "livre" in user_message or "disponible" in user_message or "available" in user_message:
            books = get_books_from_db()
            if books:
                # Format up to 5 books
                book_list = []
                for book in books[:5]:
                    category = book.get('category_name', 'Non catégorisé')
                    book_list.append(f"📖 {book['titre']} de {book['auteur']} ({category})")

                response = "Voici nos livres disponibles :\n\n" + "\n".join(book_list)

                if len(books) > 5:
                    response += f"\n\nEt {len(books) - 5} autres livres ! Consultez la section 'Livres' pour voir toute notre collection."

                return jsonify({"response": response})
            else:
                return jsonify({"response": "Actuellement, nous mettons à jour notre collection. Revenez bientôt pour découvrir de nouveaux livres !"})

        # Handle search queries
        elif "trouve" in user_message or "cherche" in user_message or "recherche" in user_message:
            # Extract search terms (simple approach)
            search_terms = [word for word in user_message.split() if len(word) > 2 and word not in ['livre', 'trouve', 'cherche', 'recherche', 'un', 'une', 'les', 'des', 'pour', 'dans', 'sur', 'avec']]
            if search_terms:
                books = search_books_in_db(' '.join(search_terms))
                if books:
                    book_list = [f"📖 {book['titre']} de {book['auteur']}" for book in books]
                    return jsonify({"response": "J'ai trouvé ces livres :\n\n" + "\n".join(book_list)})
                else:
                    return jsonify({"response": "Je n'ai pas trouvé de livres correspondant à votre recherche. Essayez d'autres mots-clés."})

        # Handle category queries
        elif "catégorie" in user_message or "category" in user_message:
            categories = get_categories_from_db()
            if categories:
                category_list = [f"📚 {cat['nom']}" for cat in categories]
                return jsonify({"response": "Voici nos catégories :\n\n" + "\n".join(category_list)})
            else:
                return jsonify({"response": "Découvrez tous nos livres dans la section 'Livres' de notre bibliothèque !"})

        # Handle reservation queries
        elif "réservation" in user_message or "réserver" in user_message or "reserve" in user_message:
            return jsonify({"response": "Pour réserver un livre, connectez-vous et allez dans la section 'Réservations'. Vous pourrez y voir tous les livres disponibles et faire votre demande."})

        # Handle due date queries
        elif "date" in user_message or "retour" in user_message or "due" in user_message:
            return jsonify({"response": "Les dates de retour dépendent de chaque réservation. Connectez-vous et allez dans 'Mes Réservations' pour voir vos dates limites de retour."})

        # Handle help queries
        elif "aide" in user_message or "help" in user_message or "comment" in user_message:
            return jsonify({"response": "Je peux vous aider avec :\n\n📚 Livres disponibles et recherche\n📖 Informations sur nos collections\n🗓️ Réservations et dates de retour\n📂 Catégories et organisation\n\nQue voulez-vous savoir sur BookHive ?"})

        # Handle greeting queries
        elif "bonjour" in user_message or "salut" in user_message or "hello" in user_message:
            return jsonify({"response": "Bonjour ! 👋 Bienvenue sur BookHive, votre bibliothèque en ligne. Je suis là pour vous aider à découvrir nos livres, faire des réservations, et répondre à toutes vos questions. Comment puis-je vous assister aujourd'hui ?"})

        # Default response
        else:
            return jsonify({"response": "Je suis votre assistant BookHive ! Je peux vous aider avec les livres disponibles, les réservations, les dates de retour, et les informations sur nos collections. Que voulez-vous savoir ?"})

    except Exception as e:
        print(f"Chat error: {e}")
        return jsonify({"response": "Désolé, j'ai rencontré une erreur. Pouvez-vous reformuler votre question ?"})

if __name__ == "__main__":
    print("🚀 Starting BookHive Chatbot Server...")
    print("📚 Connected to bookshare database")
    app.run(port=8001, debug=True, host='0.0.0.0')
