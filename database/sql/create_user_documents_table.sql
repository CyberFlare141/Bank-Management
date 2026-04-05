CREATE TABLE user_documents (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    account_type ENUM('normal', 'student') NOT NULL,
    nid_or_birth_certificate VARCHAR(255) NOT NULL,
    photo VARCHAR(255) NOT NULL,
    job_id VARCHAR(255) NULL,
    student_id VARCHAR(255) NULL,
    electric_bill VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT user_documents_user_id_unique UNIQUE (user_id),
    CONSTRAINT fk_user_documents_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT chk_user_documents_account_type CHECK (
        (account_type = 'normal' AND job_id IS NOT NULL AND student_id IS NULL)
        OR
        (account_type = 'student' AND student_id IS NOT NULL AND job_id IS NULL)
    )
);
