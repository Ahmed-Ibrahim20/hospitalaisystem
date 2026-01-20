# Team Member 3 — Explainability (SHAP) & Model Monitoring (EN)

## Scope of Work

Responsible for making predictions explainable and for adding monitoring components to support safe clinical use over time.

## What I Implemented

-   Integrated **SHAP (SHapley Additive exPlanations)** to provide per-patient feature contribution explanations.
-   Designed explanation output format suitable for a hospital UI:
    -   Top contributing features
    -   Direction of impact (increases/decreases risk)
    -   Human-readable descriptions where applicable
-   Added risk factor analysis utilities that translate raw inputs into interpretable clinical categories:
    -   Cardiovascular risk score and level
    -   Lifestyle risk score and level
    -   BMI category
    -   Age-based risk flags
-   Implemented prediction logging and monitoring foundations:
    -   Prediction logging hooks
    -   Monitoring report endpoint support (`/monitoring/report`)
    -   Drift/performance tracking concepts documented for production rollout

## Key Technical Decisions

-   SHAP was selected because it provides:
    -   Local explanations per individual patient
    -   Consistent additive attribution suitable for medical interpretation
    -   Compatibility with tree ensembles (fast TreeExplainer)
-   Monitoring was designed as a minimal, hospital-compatible starting point that can evolve into full Prometheus/Grafana dashboards.

## Where It Lives in the Project

-   FastAPI explainability path:
    -   `AI-Powered/deployment/fastapi_service_advanced.py`
-   Monitoring utilities:
    -   `AI-Powered/deployment/monitoring.py`
-   Conceptual documentation:
    -   `AI-Powered/COMPLETE_SYSTEM_GUIDE.md`

## Deliverables

-   SHAP explanation support in prediction responses
-   Risk factor analysis and recommendation logic hooks
-   Monitoring report interface for operational visibility

## Defense / Presentation Talking Points

-   Why explainability is mandatory in clinical decision support
-   How SHAP supports clinician trust and auditability
-   Why monitoring and drift detection matter after deployment
