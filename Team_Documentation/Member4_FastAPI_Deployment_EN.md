# Team Member 4 — FastAPI Service & Deployment (EN)

## Scope of Work

Responsible for building the Python inference service (FastAPI), defining the API contracts, and preparing deployment configuration.

## What I Implemented

-   Implemented the **FastAPI inference service** for diabetes prediction.
-   Defined strong input validation using Pydantic models:
    -   Enforced feature ranges (e.g., BMI 10–100, Age group 1–13, binary flags 0/1)
    -   Reduced risk of invalid inputs reaching the model
-   Implemented core endpoints:
    -   `GET /health` (service readiness)
    -   `POST /predict` (single prediction, optional SHAP)
    -   `POST /predict/batch` (batch prediction)
    -   `GET /model/info` (model metadata)
    -   `GET /monitoring/report` (monitoring overview)
-   Enabled CORS for integration with the hospital web application.
-   Prepared deployment-ready structure:
    -   Requirements files
    -   Docker-oriented deployment examples documented

## Key Technical Decisions

-   REST API design to support Laravel integration with predictable JSON schemas.
-   Startup model loading with fallback behavior when advanced model artifacts are missing.
-   Background logging to avoid slowing down interactive clinical workflows.

## Where It Lives in the Project

-   FastAPI service:
    -   `AI-Powered/deployment/fastapi_service_advanced.py`
-   Deployment assets:
    -   `AI-Powered/deployment/`
    -   `AI-Powered/requirements.txt`

## Deliverables

-   Working API service with strict validation and well-defined endpoints
-   Operational endpoints for health/model information
-   Deployment-ready structure for local or server usage

## Defense / Presentation Talking Points

-   Why strong validation is critical in medical pipelines
-   How response time is maintained while providing explanations
-   How the service is designed for extension to multi-disease endpoints
