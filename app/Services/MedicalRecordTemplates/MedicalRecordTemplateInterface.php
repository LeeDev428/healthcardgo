<?php

namespace App\Services\MedicalRecordTemplates;

interface MedicalRecordTemplateInterface
{
    /**
     * Get the template fields configuration
     */
    public static function getFields(): array;

    /**
     * Get the template name
     */
    public static function getTemplateName(): string;

    /**
     * Get the template category
     */
    public static function getCategory(): string;
}
