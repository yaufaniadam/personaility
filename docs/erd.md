erDiagram

    USERS {
        bigint id PK
        varchar name
        varchar email
        varchar password
        enum role
        enum subscription_status
        timestamp subscription_expires_at
        timestamp created_at
        timestamp updated_at
    }

    QUESTIONS {
        bigint id PK
        enum trait
        text question_text
        boolean is_reverse
        boolean allow_note
        int order_number
        boolean active
        timestamp created_at
        timestamp updated_at
    }

    ASSESSMENTS {
        bigint id PK
        bigint user_id FK
        decimal openness_score
        decimal conscientiousness_score
        decimal extraversion_score
        decimal agreeableness_score
        decimal neuroticism_score
        int openness_percentile
        int conscientiousness_percentile
        int extraversion_percentile
        int agreeableness_percentile
        int neuroticism_percentile
        varchar version
        timestamp completed_at
        timestamp created_at
        timestamp updated_at
    }

    ASSESSMENT_ANSWERS {
        bigint id PK
        bigint assessment_id FK
        bigint question_id FK
        tinyint likert_score
        text note_text
        timestamp created_at
        timestamp updated_at
    }

    AI_INSIGHTS {
        bigint id PK
        bigint assessment_id FK
        text core_strength
        text blind_spot
        text growth_suggestion
        text stress_pattern
        longtext raw_prompt
        longtext raw_response
        varchar model_version
        timestamp created_at
        timestamp updated_at
    }

    PSYCHOLOGISTS {
        bigint id PK
        varchar name
        varchar str_number
        varchar sip_number
        varchar city
        varchar province
        varchar specialization
        varchar contact_phone
        varchar contact_whatsapp
        varchar website
        text bio
        boolean verified_status
        boolean active
        timestamp created_at
        timestamp updated_at
    }

    USER_ACTIVITY_LOGS {
        bigint id PK
        bigint user_id FK
        varchar activity_type
        json metadata_json
        timestamp created_at
    }

    USERS ||--o{ ASSESSMENTS : has
    USERS ||--o{ USER_ACTIVITY_LOGS : logs

    ASSESSMENTS ||--o{ ASSESSMENT_ANSWERS : contains
    ASSESSMENTS ||--|| AI_INSIGHTS : generates

    QUESTIONS ||--o{ ASSESSMENT_ANSWERS : referenced_by