<?php
namespace Uphf\GestionAbsence\Model\Entity\Account;

/**
 * Enumeration représentant les types de compte
 */
enum AccountType : String {
    case Student = 'Student';
    case Teacher = 'Teacher';
    case EducationalManager = 'EducationalManager';
    case Secretary = 'Secretary';
}