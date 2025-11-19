# main.py
import os
import json
import re
from fastapi import FastAPI, HTTPException
from pydantic import BaseModel
from google import genai
from google.genai import types
from typing import Optional
from fastapi.middleware.cors import CORSMiddleware
from dotenv import load_dotenv

# Load environment variables
load_dotenv()

app = FastAPI()

# Setup CORS
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# === CONFIG GENAI ===
api_key = os.getenv("GOOGLE_API_KEY")
if not api_key:
    print("WARNING: GOOGLE_API_KEY tidak ditemukan di .env!")

try:
    client = genai.Client(api_key=api_key)
except Exception as e:
    print(f"Error Init Client: {e}")

# === MODEL DATA ===
class UserProfile(BaseModel):
    username: str
    gender: str
    age: int
    weight: float
    height: float
    fitness_goal: str
    fitness_level: str
    equipment_access: str
    days_per_week: int
    minutes_per_session: int
    injuries: str

def clean_json(raw_text):
    # """
    # Membersihkan output dari Gemini agar bisa diparsing sebagai JSON.
    # Menangani markdown, teks tambahan, dan format aneh.
    # """
    print(f"--- RAW AI OUTPUT ---\n{raw_text}\n---------------------")
    
    try:
        # 1. Coba load langsung (best case)
        return json.loads(raw_text)
    except json.JSONDecodeError:
        pass

    # 2. Hapus Markdown (```json ... ```)
    pattern = r"```json\s*(.*?)\s*```"
    match = re.search(pattern, raw_text, re.DOTALL)
    if match:
        text_content = match.group(1)
    else:
        # Kalau gak ada markdown, cari kurung kurawal terluar
        start = raw_text.find('{')
        end = raw_text.rfind('}') + 1
        if start != -1 and end != 0:
            text_content = raw_text[start:end]
        else:
            text_content = raw_text

    # 3. Bersihin trailing commas (biasanya bikin error di JSON standar)
    # Regex ini hapus koma sebelum kurung tutup/siku tutup
    text_content = re.sub(r',\s*([\]}])', r'\1', text_content)
    
    # 4. Coba parse lagi setelah pembersihan
    try:
        return json.loads(text_content)
    except json.JSONDecodeError as e:
        print(f"Error parsing cleaned JSON: {e}")
        print(f"Cleaned text content: {text_content}")
        raise ValueError("Failed to parse JSON after cleaning.")

@app.post("/generate-plan")
async def generate_plan(user: UserProfile):
    try:
        # 1. Construct Prompt
        prompt = f"""
        Buatkan rencana latihan mingguan (weekly workout plan) dalam format JSON untuk pengguna berikut:
        
        Nama: {user.username}
        Jenis Kelamin: {user.gender}
        Umur: {user.age} tahun
        Berat: {user.weight} kg
        Tinggi: {user.height} cm
        Tujuan: {user.fitness_goal}
        Level: {user.fitness_level}
        Akses Alat: {user.equipment_access}
        Hari Latihan/Minggu: {user.days_per_week} hari
        Durasi/Sesi: {user.minutes_per_session} menit
        Cedera: {user.injuries}

        Output HARUS berupa JSON valid dengan struktur berikut (tanpa teks pembuka/penutup lain):
        {{
            "plan_name": "Nama Program (Contoh: Hypertrophy Push Pull Legs)",
            "coach_note": "Pesan motivasi atau saran singkat dari coach AI untuk user ini",
            "weekly_schedule": [
                {{
                    "week_number": 1,
                    "day_number": 1,
                    "day_name": "Senin",
                    "session_title": "Chest & Triceps Focus",
                    "is_off_day": false,
                    "exercises": [
                        {{
                            "name": "Push Up",
                            "sets": "3",
                            "reps": "10-12",
                            "rest": "60s"
                        }}
                    ]
                }}
            ]
        }}
        
        PENTING:
        1. Buatkan HANYA 1 MINGGU (7 HARI) rencana latihan sebagai template.
        2. "weekly_schedule" harus berisi array object untuk 7 hari (Day 1 sampai Day 7).
        3. Jika user latihan 3 hari/minggu, pastikan ada 4 hari istirahat (is_off_day: true).
        4. Urutkan dari Hari 1 sampai Hari 7.
        """

        # 2. Call Gemini
        response = client.models.generate_content(
            model="gemini-2.0-flash",
            contents=[prompt],
            config=types.GenerateContentConfig(
                response_mime_type="application/json",
                temperature=0.7
            )
        )

        # 3. Parsing Output
        if not response.text:
            raise HTTPException(status_code=500, detail="AI tidak memberikan respon.")
            
        plan_data = clean_json(response.text)
        return plan_data

    except Exception as e:
        print(f"SERVER ERROR: {str(e)}")
        raise HTTPException(status_code=500, detail=f"Terjadi kesalahan: {str(e)}")

# Jalankan dengan: uvicorn main:app --reload