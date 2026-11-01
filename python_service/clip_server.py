"""
Antik Sikke Vektör Benzerlik Arama Servisi (FastAPI + CLIP)
Çalıştırma: uvicorn clip_server:app --host 0.0.0.0 --port 8000
Gereksinimler: pip install fastapi uvicorn torch torchvision sentence-transformers pillow numpy
"""

import io
from typing import List
from fastapi import FastAPI, File, Form, UploadFile, HTTPException
from fastapi.middleware.cors import CORSMiddleware
import numpy as np
from PIL import Image
from sentence_transformers import SentenceTransformer

app = FastAPI(
    title="Numismatic CLIP Vector Search API",
    description="Antik sikke ön/arka yüz fotoğraflarından CLIP vektörü çıkarıp benzerlik sıralaması yapar.",
    version="1.0.0"
)

app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# 1. CLIP Modelini Yükle (ViT-B/32 - 512 Boyutlu Vektör)
print("CLIP modeli belleğe yükleniyor...")
model = SentenceTransformer('clip-ViT-B-32')
print("Model başarıyla yüklendi!")

# Örnek sikke vektör veritabanı önbelleği (ID -> 512 boyutlu normalize vektör)
# Gerçek ortamda burası Qdrant, Milvus veya MySQL 8.4+ Vector tablosundan okunur
INDEXED_COIN_VECTORS = {
    1: np.random.randn(512), # Örn: Trajan Denarius
    2: np.random.randn(512), # Örn: Büyük İskender Tetradrahmi
    3: np.random.randn(512), # Örn: Fatih Sultan Mehmed Sultani
}

# Vektörleri L2 normalize et
for cid in INDEXED_COIN_VECTORS:
    INDEXED_COIN_VECTORS[cid] /= np.linalg.norm(INDEXED_COIN_VECTORS[cid])


def extract_clip_embedding(image_bytes: bytes) -> np.ndarray:
    """Görselden 512 boyutlu normalize CLIP vektörü çıkarır."""
    image = Image.open(io.BytesIO(image_bytes)).convert("RGB")
    embedding = model.encode(image)
    normalized = embedding / np.linalg.norm(embedding)
    return normalized


@app.post("/search/similar")
async def search_similar_coins(
    obverse_image: UploadFile = File(...),
    reverse_image: UploadFile = File(...),
    top_k: int = Form(5)
):
    """
    Mobil kameradan gelen ön ve arka yüz görsellerini vektörleştirir
    ve en yakın sikkeleri kosinüs benzerliğiyle döndürür.
    """
    try:
        obverse_bytes = await obverse_image.read()
        reverse_bytes = await reverse_image.read()

        # 1. Her iki yüzün embeddingini çıkar
        emb_obverse = extract_clip_embedding(obverse_bytes)
        emb_reverse = extract_clip_embedding(reverse_bytes)

        # 2. Ön ve arka yüzün ortalama temsil vektörünü hesapla
        query_vector = (emb_obverse + emb_reverse) / 2.0
        query_vector /= np.linalg.norm(query_vector)

        # 3. Kosinüs Benzerliği Hesaplama (Cosine Similarity = Dot Product)
        scores = []
        for coin_id, coin_vec in INDEXED_COIN_VECTORS.items():
            similarity = float(np.dot(query_vector, coin_vec))
            # Pozitif ölçeklendirme [-1, 1] -> [0, 1]
            adjusted_sim = max(0.0, (similarity + 1) / 2.0)
            scores.append({
                "coin_id": coin_id,
                "similarity": round(adjusted_sim, 4)
            })

        # En yüksek benzerliğe göre sırala
        scores.sort(key=lambda x: x["similarity"], reverse=True)
        top_matches = scores[:top_k]

        return {
            "success": True,
            "vector_dimension": 512,
            "matches": top_matches
        }

    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Vektör çıkarma hatası: {str(e)}")


@app.post("/embed")
async def get_image_embedding(image: UploadFile = File(...)):
    """Kataloğa yeni sikke eklenirken indeksleme için vektör çıkarır."""
    image_bytes = await image.read()
    vec = extract_clip_embedding(image_bytes)
    return {
        "success": True,
        "embedding": vec.tolist()
    }

