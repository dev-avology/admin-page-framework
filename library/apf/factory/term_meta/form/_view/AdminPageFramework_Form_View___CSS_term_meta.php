<?php
/*
 * Admin Page Framework v3.9.1 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/admin-page-framework-compiler>
 * <https://en.michaeluno.jp/admin-page-framework>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AdminPageFramework_Form_View___CSS_term_meta extends AdminPageFramework_Form_View___CSS_Base {
    protected function _get()
    {
        return $this->_getRules();
    }
    private function _getRules()
    {
        return <<<CSSRULES
.admin-page-framework-form-table-outer-row-term_meta,.admin-page-framework-form-table-outer-row-term_meta>td{margin:0;padding:0}.admin-page-framework-form-table-term_meta>tbody>tr>td{margin-left:0;padding-left:0}.admin-page-framework-form-table-term_meta .admin-page-framework-sectionset,.admin-page-framework-form-table-term_meta .admin-page-framework-section{margin-bottom:0}.admin-page-framework-form-table-term_meta.add-new-term .title-colon{margin-left:.2em}.admin-page-framework-form-table-term_meta.add-new-term .admin-page-framework-section .form-table>tbody>tr>td,.admin-page-framework-form-table-term_meta.add-new-term .admin-page-framework-section .form-table>tbody>tr>th{display:inline-block;width:100%;padding:0;float:right;clear:right}.admin-page-framework-form-table-term_meta.add-new-term .admin-page-framework-field{width:auto}.admin-page-framework-form-table-term_meta.add-new-term .admin-page-framework-field{max-width:100%}.admin-page-framework-form-table-term_meta.add-new-term .sortable .admin-page-framework-field{width:auto}.admin-page-framework-form-table-term_meta.add-new-term .admin-page-framework-section .form-table>tbody>tr>th{font-size:13px;line-height:1.5;margin:0;font-weight:700}.admin-page-framework-form-table-term_meta .admin-page-framework-section-title h3{border:none;font-weight:700;font-size:1.12em;margin:0;padding:0;font-family:'Open Sans',sans-serif;cursor:inherit;-webkit-user-select:inherit;-moz-user-select:inherit;user-select:inherit}.admin-page-framework-form-table-term_meta .admin-page-framework-collapsible-title h3{margin:0}.admin-page-framework-form-table-term_meta h4{margin:1em 0;font-size:1.04em}.admin-page-framework-form-table-term_meta .admin-page-framework-section-tab h4{margin:0}
CSSRULES;
    }
}
