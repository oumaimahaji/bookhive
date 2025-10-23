from fastapi import FastAPI
from pydantic import BaseModel
from typing import List
from transformers import pipeline

app = FastAPI(title="AI Review Summarizer")

summarizer = pipeline("summarization", model="facebook/bart-large-cnn")

class ReviewsIn(BaseModel):
    reviews: List[str]

@app.post("/summarize")
def summarize_reviews(data: ReviewsIn):
    all_text = " ".join(data.reviews)
    summary = summarizer(all_text, max_length=150, min_length=50, do_sample=False)
    return {"summary": summary[0]['summary_text']}
