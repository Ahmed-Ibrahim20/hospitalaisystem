# Team Member 1 — Data Pipeline & Feature Engineering (EN)

## Scope of Work

Responsible for preparing the BRFSS 2015 diabetes datasets and producing a reliable, model-ready feature space used by the ML layer.

## What I Implemented

-   Cleaned and validated BRFSS 2015 datasets used by the project:
    -   `diabetes_binary_health_indicators_BRFSS2015.csv` (253,680 records; imbalanced)
    -   `diabetes_binary_5050split_health_indicators_BRFSS2015.csv` (balanced training split)
    -   `diabetes_012_health_indicators_BRFSS2015.csv` (3-class target)
-   Defined the **21 core clinical/behavioral features** and ensured consistent typing, ranges, and encoding.
-   Designed and implemented **30 engineered features** to improve signal quality and clinical relevance:
    -   Medical ratios (e.g., `health_age_ratio`, `bmi_activity_ratio`)
    -   Risk flags (e.g., obesity/severe obesity, age risk, mental/physical health risk)
    -   Composite indicators (e.g., extended cardio risk, lifestyle score, socioeconomic risk)
    -   Interaction features (e.g., age–BMI, age–BP) and global risk counters/scores
-   Prepared train/test split strategy and supported balancing strategy for robust evaluation.

## Key Technical Decisions

-   Feature engineering was designed to be:
    -   Clinically interpretable (ratios/flags aligned with risk factors)
    -   Model-agnostic (works for RF/XGBoost/LightGBM/Ensembles)
    -   Extendable to future diseases (heart disease, hypertension)

## Where It Lives in the Project

-   Data specification and feature definitions:
    -   `Project_Documentation_English.md`
    -   `AI-Powered/COMPLETE_SYSTEM_GUIDE.md`
-   Datasets:
    -   `AI-Powered/DataSet/`

## Deliverables

-   Documented dataset properties and feature schema (original + engineered)
-   Provided model-ready data conventions (ranges, encodings, validation rules)

## Defense / Presentation Talking Points

-   Why BRFSS is suitable for population-level screening and risk modeling
-   Why engineered features matter (signal enrichment + clinical meaning)
-   How the same feature layer supports scalability to other chronic diseases
