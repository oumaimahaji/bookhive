#!/usr/bin/env python3
import sys
import json
import re

def analyze_sentiment(text):
    """
    SIMPLE TEST VERSION - Force negative for "don't like"
    """
    print(f"TEST: Analyzing: '{text}'", file=sys.stderr)
    
    text_lower = text.lower()
    
    # IMMEDIATELY return negative for any "don't like" pattern
    if "don't like" in text_lower or "do not like" in text_lower:
        print("TEST: Found 'don't like' - FORCING NEGATIVE", file=sys.stderr)
        return json.dumps({
            'sentiment': 'negative',
            'confidence': 0.95,
            'polarity': -0.9,
            'method': 'dont_like_immediate'
        })
    
    # Also check for French negative patterns
    if "n'aime pas" in text_lower or "je n'aime" in text_lower:
        print("TEST: Found French negative - FORCING NEGATIVE", file=sys.stderr)
        return json.dumps({
            'sentiment': 'negative',
            'confidence': 0.95,
            'polarity': -0.9,
            'method': 'french_negative'
        })
    
    # If no clear negative patterns, return neutral
    print("TEST: No clear negative patterns - returning neutral", file=sys.stderr)
    return json.dumps({
        'sentiment': 'neutral', 
        'confidence': 0.8,
        'polarity': 0.0,
        'method': 'fallback'
    })

if _name_ == "_main_":
    if len(sys.argv) > 1:
        text = ' '.join(sys.argv[1:])
        result = analyze_sentiment(text)
        print(result)
    else:
        text = sys.stdin.read().strip()
        if text:
            result = analyze_sentiment(text)
            print(result)