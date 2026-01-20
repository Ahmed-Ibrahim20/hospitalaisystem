# Team Member 5 — Laravel Backend, Integration & UI (EN)

## Scope of Work

Responsible for the hospital web system (Laravel) and the integration layer that connects clinical workflows to the FastAPI service.

## What I Implemented

-   Built the Laravel 12 backend structure with:
    -   MVC + service layer
    -   MySQL persistence and migrations
    -   Authentication (Laravel Breeze) and role-based access control
-   Implemented hospital workflows and entities:
    -   Patients, encounters/visits, and prediction records
    -   Storage of model outputs (probability, risk level, SHAP, recommendations)
-   Implemented AI integration via a dedicated service:
    -   `app/Services/AIPredictionService.php`
    -   External calls to FastAPI using Guzzle
    -   Health checks and model info retrieval
    -   Batch prediction support
    -   Safe fallback behavior (demo predictions) to keep UI functional when the API is unavailable
-   Integrated routes and pages for AI usage:
    -   AI dashboard and prediction pages
    -   Reviewing/approving predictions by clinicians
    -   Reports/statistics pages for hospital oversight

## Where It Lives in the Project

-   Integration service:
    -   `app/Services/AIPredictionService.php`
-   Routes:
    -   `routes/web.php` (AI routes under `ai/` prefix)
-   UI views:
    -   `resources/views/` (AI views and dashboards)

## Deliverables

-   End-to-end integration: encounter → API prediction → database storage → clinician review
-   Role-secured workflows suitable for a hospital setting

## Defense / Presentation Talking Points

-   Why integration is the main gap between research prototypes and hospital systems
-   How the system stores predictions for audit and follow-up
-   How role-based access supports governance (admin/doctor workflows)
