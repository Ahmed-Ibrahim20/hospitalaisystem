# API Reference - Diabetes Prediction Service

## Base URL
```
http://localhost:8000
```

## Authentication
```
Authorization: Bearer {token}
```

## Endpoints

### 1. Health Check
**GET** `/health`

**Response:**
```json
{
  "status": "healthy",
  "model_loaded": true,
  "model_type": "random_forest",
  "version": "1.0.0"
}
```

### 2. Predict
**POST** `/predict`

**Request Body:**
```json
{
  "HighBP": 1,
  "HighChol": 1,
  "BMI": 28.5,
  ...
}
```

**Response:**
```json
{
  "success": true,
  "prediction": 1,
  "probability": 0.73,
  "risk_level": "عالي",
  "recommendations": [...]
}
```

### 3. Model Info
**GET** `/model/info`

**Response:**
```json
{
  "model_type": "random_forest",
  "features_count": 29,
  "training_history": {...}
}
```
