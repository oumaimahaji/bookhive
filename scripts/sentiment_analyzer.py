#!/usr/bin/env python3
import sys
import json
import re

def analyze_sentiment(text):
    """
    FIXED VERSION - Proper double negation and positive word handling
    """
    try:
        text_lower = text.lower().strip()
        
        if not text or len(text) < 2:
            return json.dumps({
                'sentiment': 'neutral',
                'confidence': 1.0,
                'polarity': 0.0
            })
        
        # STRONG POSITIVE phrases (immediate positive)
        strong_positive_phrases = [
            "wonderful", "excellent", "amazing", "fantastic", "awesome", "great",
            "j'adore", "j adore", "je adore", "j'aime", "super", "génial", 
            "magnifique", "parfait", "formidable", "merveilleux"
        ]
        
        for phrase in strong_positive_phrases:
            if phrase in text_lower:
                return json.dumps({
                    'sentiment': 'positive',
                    'confidence': 0.95,
                    'polarity': 0.9,
                    'method': 'strong_positive'
                })
        
        # STRONG NEGATIVE phrases (immediate negative)
        strong_negative_phrases = [
            "don't like", "do not like", "i don't like", "i do not like",
            "n'aime pas", "je n'aime", "j'aime pas", "je déteste", "j'aime pas",
            "i hate", "i dislike", "it's bad", "it is bad", "this is terrible",
            "horrible", "terrible", "awful", "worst"
        ]
        
        for phrase in strong_negative_phrases:
            if phrase in text_lower:
                return json.dumps({
                    'sentiment': 'negative',
                    'confidence': 0.95,
                    'polarity': -0.9,
                    'method': 'strong_negative'
                })
        
        # Word lists
        positive_words = {
            'excellent': 3, 'super': 3, 'génial': 3, 'magnifique': 3, 'parfait': 3,
            'formidable': 3, 'fantastique': 3, 'merveilleux': 3, 'wonderful': 3,
            'amazing': 3, 'awesome': 3, 'great': 3, 'fantastic': 3,
            'bon': 2, 'utile': 2, 'agréable': 2, 'content': 2, 'heureux': 2,
            'satisfait': 2, 'aimer': 2, 'adorer': 3, 'recommandé': 2, 'bravo': 2,
            'bien': 1, 'top': 2, 'cool': 1, 'sympa': 1, 'love': 3, 'like': 2,
            'good': 2, 'nice': 2, 'best': 3
        }
        
        negative_words = {
            'mauvais': 3, 'nul': 3, 'horrible': 3, 'terrible': 3, 'déçu': 3,
            'décevant': 3, 'mediocre': 2, 'pire': 3, 'insupportable': 3,
            'ennuyeux': 2, 'inutile': 3, 'probleme': 2, 'bug': 2, 'erreur': 2,
            'hate': 3, 'bad': 3, 'worst': 4, 'dislike': 3, 'awful': 3,
            'détester': 3, 'haïr': 4
        }
        
        # Calculate scores
        positive_score = 0
        negative_score = 0
        
        words = re.findall(r'\b\w+\b', text_lower)
        
        # SPECIAL CASE: Double negation "pas mal" = positive
        if "pas mal" in text_lower or "not bad" in text_lower:
            return json.dumps({
                'sentiment': 'positive',
                'confidence': 0.85,
                'polarity': 0.7,
                'method': 'double_negation'
            })
        
        # Score individual words
        negation_active = False
        i = 0
        while i < len(words):
            word = words[i]
            
            # Check for negation words
            if word in ['pas', 'not', 'non', 'no', 'ne']:
                negation_active = True
                i += 1
                continue
            
            # Score the word with negation context
            if word in positive_words:
                if negation_active:
                    # "pas bon" = not good = negative
                    negative_score += positive_words[word] * 1.5
                else:
                    positive_score += positive_words[word]
            
            elif word in negative_words:
                if negation_active:
                    # "pas mauvais" = not bad = positive  
                    positive_score += negative_words[word] * 1.5
                else:
                    negative_score += negative_words[word]
            
            # Reset negation after one word
            negation_active = False
            i += 1
        
        # Calculate result
        total_score = positive_score + negative_score
        
        if total_score == 0:
            sentiment = 'neutral'
            confidence = 0.8
            polarity = 0.0
        else:
            polarity = (positive_score - negative_score) / total_score
            
            # More sensitive thresholds
            if polarity > 0.1:
                sentiment = 'positive'
                confidence = min(0.95, (polarity + 1) / 2)
            elif polarity < -0.1:
                sentiment = 'negative'
                confidence = min(0.95, (1 - polarity) / 2)
            else:
                sentiment = 'neutral'
                confidence = 0.8
        
        # Boost confidence for clear cases
        if abs(polarity) > 0.5:
            confidence = min(0.95, confidence * 1.2)
        
        # Final confidence adjustment
        confidence = max(0.5, min(0.95, confidence))
        
        result = {
            'sentiment': sentiment,
            'confidence': round(confidence, 2),
            'polarity': round(polarity, 3),
            'positive_score': positive_score,
            'negative_score': negative_score,
            'method': 'word_scoring'
        }
        
        return json.dumps(result)
        
    except Exception as e:
        return json.dumps({
            'sentiment': 'neutral',
            'confidence': 1.0,
            'polarity': 0.0,
            'error': str(e)
        })

if __name__ == "__main__":
    if len(sys.argv) > 1:
        text = ' '.join(sys.argv[1:])
        result = analyze_sentiment(text)
        print(result)
    else:
        text = sys.stdin.read().strip()
        if text:
            result = analyze_sentiment(text)
            print(result)