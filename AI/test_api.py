import requests
import json

url = "http://localhost:8000/generate-plan"
data = {
    "username": "TestUser",
    "gender": "Male",
    "age": 25,
    "weight": 70,
    "height": 175,
    "fitness_goal": "Muscle Gain",
    "fitness_level": "Intermediate",
    "equipment_access": "Gym",
    "days_per_week": 3,
    "minutes_per_session": 60,
    "injuries": "None"
}

try:
    response = requests.post(url, json=data)
    print(f"Status Code: {response.status_code}")
    if response.status_code == 200:
        print("Response JSON:")
        print(json.dumps(response.json(), indent=2))
    else:
        print("Error Response:")
        print(response.text)
except Exception as e:
    print(f"Failed to connect: {e}")
