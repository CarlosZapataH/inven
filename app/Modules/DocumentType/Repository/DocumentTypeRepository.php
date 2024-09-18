<?php
require_once __DIR__ . '/../../Common/Repositories/CommonRepository.php';
require_once __DIR__ . '/../../../Models/DocumentType.php';
require_once __DIR__ . '/../Contract/IDocumentType.php';

class DocumentTypeRepository extends CommonRepository implements IDocumentType {
    public function __construct() {
        parent::__construct(DocumentType::class);
    }
}