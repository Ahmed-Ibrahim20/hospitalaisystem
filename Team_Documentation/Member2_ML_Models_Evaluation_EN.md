# Team Member 2 — Machine Learning Models & Evaluation (EN)

## Scope of Work

Responsible for developing and evaluating the diabetes prediction models, selecting high-performing algorithms, and preparing models for deployment.

## What I Implemented

-   Built and compared multiple ML models for diabetes risk prediction:
    -   Logistic Regression (baseline)
    -   Decision Tree (baseline)
    -   Random Forest
    -   Gradient Boosting
    -   XGBoost
    -   LightGBM
-   Designed and validated ensemble strategies:
    -   Voting (soft voting)
    -   Stacking (Logistic Regression as meta-learner)
-   Tuned key hyperparameters to achieve strong generalization.
-   Reported performance using clinically relevant metrics:
    -   Accuracy (target ≥ 87%)
    -   ROC-AUC (target ≥ 0.87)
    -   Additional metrics as needed (precision/recall/F1)
-   Ensured compatibility between training pipeline and deployed inference pipeline (feature order and validation).

## Key Technical Decisions

-   Chose gradient-boosted trees (XGBoost/LightGBM) as primary candidates due to:
    -   Strong performance on tabular clinical risk factors
    -   Robustness to non-linear feature interactions
    -   Compatibility with SHAP explanations
-   Used ensembles to reduce variance and improve stability across patient subgroups.

## Where It Lives in the Project

-   ML model code and saved artifacts:
    -   `AI-Powered/models/`
    -   Saved models referenced by:
        -   `AI-Powered/deployment/fastapi_service_advanced.py`
-   Model comparison summary:
    -   `AI-Powered/COMPLETE_SYSTEM_GUIDE.md`

## Deliverables

-   Trained models (including an advanced model with SHAP support)
-   Performance table and model selection rationale
-   Deployment-ready model artifacts

## Defense / Presentation Talking Points

-   Why tree-based models dominate for structured clinical risk prediction
-   How ensemble learning improves robustness
-   Interpreting accuracy and ROC-AUC in a screening-oriented hospital workflow
