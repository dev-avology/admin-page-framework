<?php
/**
 * Admin Page Framework Loader
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2022, Michael Uno; Licensed GPLv2
 */

/**
 * Adds the 'Compiler' section to the 'Compiler' tab.
 *
 * @since 3.5.4
 */
class AdminPageFrameworkLoader_AdminPage_Tool_Compiler_Compiler extends AdminPageFrameworkLoader_AdminPage_Section_Base {

    /**
     * Stores the admin page factory object.
     * @var AdminPageFramework
     */
    public $oFactory;

    /**
     * A user constructor.
     *
     * @param AdminPageFramework $oFactory
     * @since 3.5.4
     */
    protected function construct( $oFactory ) {

        // Store the factory object in a property.
        $this->oFactory = $oFactory;

        add_action(
            'export_name_' . $this->sPageSlug . '_' . $this->sTabSlug,
            array( $this, 'replyToFilterFileName' ),
            10,
            5
        );
        add_action(
            // export_{instantiated class name}_{section id}_{field id}
            "export_{$oFactory->oProp->sClassName}_{$this->sSectionID}_download",
            array( $this, 'replyToDownloadFramework' ),
            10,
            4
        );
        add_action(
            "export_header_{$oFactory->oProp->sClassName}_{$this->sSectionID}",
            array( $this, 'replyToGetExportHTTPHeaderModified' ),
            10,
            6
        );

    }

    /**
     * Adds form fields.
     * @since 3.5.4
     */
    public function addFields( $oFactory, $sSectionID ) {

        $oFactory->addSettingFields(
            $sSectionID, // the target section id
            array(
                'field_id'          => 'version',
                'title'             => __( 'Version', 'admin-page-framework-loader' ),
                'type'              => 'text',
                'save'              => false,
                'value'             => AdminPageFramework_Registry::VERSION,
                'attributes'        => array(
                    'size'          => 20,
                    'readonly'      => 'readonly',
                ),
            ),
            array(
                'field_id'          => 'class_prefix',
                'title'             => __( 'Class Prefix', 'admin-page-framework-loader' ),
                'type'              => 'text',
                'tip'               => array(
                    __( 'Set alphanumeric characters for the class names.', 'admin-page-framework-loader' ),
                    __( 'For example, if you set here <code>MyPluginName_</code>, you will need to extend the class named <code>MyClassName_AdminPageFramework</code> instead of <code>AdminPageFramework</code>.', 'admin-page-framework-loader' ),
                ),
                'description'       => 'e.g.<code>MyPluginName_</code>',
                'attributes'        => array(
                    'size'          => 30,
                    // 'required' => 'required',
                    'placeholder'   => __( 'Type a prefix.', 'admin-page-framework-loader' ),
                ),
            ),
            array(
                'field_id'          => 'text_domain',
                'title'             => __( 'Text Domain', 'admin-page-framework-loader' ),
                'type'              => 'text',
                'tip'               => __( 'The default text domain of your project.', 'admin-page-framework-loader' ),
                'description'       => 'e.g.<code>my-plugin</code>',
                'attributes'        => array(
                    'size'          => 40,
                    // 'required' => 'required',
                    'placeholder'   => __( 'Type your text domain.', 'admin-page-framework-loader' ),
                ),
            ),
            array(
                'field_id'          => 'components',
                'title'             => __( 'Components', 'admin-page-framework-loader' ),
                'type'              => 'checkbox',
                'description'       => array(
                    __( 'Select the components you would like to include in your framework files.', 'admin-page-framework-loader' ),
                    __( 'If you are not sure what to select, check them all.', 'admin-page-framework-loader' ),
                ),
                'label'               => $this->___getComponentLabels(),
                'default'             => array_fill_keys(
                    array_keys( $this->___getComponentLabels() ),
                    true // all true
                ),
                'select_all_button'     => true,
                'select_none_button'    => true,
                'label_min_width'       => '100%',
                'attributes'            => array(
                    'core'      => array(
                        'disabled' => 'disabled',
                    ),
                ),
            ),
            array(
                'field_id'          => 'download',
                'title'             => __( 'Compile', 'admin-page-framework-loader' ),
                'type'              => 'export',
                'label_min_width'   => 0,
                'order'             => 100,
                'value'             => __( 'Download', 'admin-page-framework-demo' ),
                'file_name'         => 'admin-page-framework.zip',  // the default file name. This will be modified by the filter.
                'format'            => 'text',  // 'json', 'text', 'array'
                'description'       => $oFactory->oUtil->getAOrB(
                    class_exists( 'ZipArchive' ),
                    __( 'Download the compiled framework files as a zip file.', 'admin-page-framework-loader' ),
                    __( 'The zip extension needs to be enabled to use this feature.', 'admin-page-framework-loader' )
                ),
                'attributes'        => array(
                    'disabled'  => $oFactory->oUtil->getAOrB(
                        class_exists( 'ZipArchive' ),
                        null,
                        'disabled'
                    ),
                ),
            )
        );

        new AdminPageFrameworkLoader_AdminPage_Tool_Compiler_CustomFieldTypes( $oFactory, $sSectionID );

    }
        /**
         * Returns component labels as an array.
         * @since       3.5.4
         * @return      array
         */
        private function ___getComponentLabels() {
            return array(
                'admin_pages'           => __( 'Admin Pages', 'admin-page-framework-loader' ),
                'network_admin_pages'   => __( 'Network Admin Pages', 'admin-page-framework-loader' ),
                'post_types'            => __( 'Custom Post Types', 'admin-page-framework-loader' ),
                'taxonomies'            => __( 'Taxonomy Fields', 'admin-page-framework-loader' ),
                'term_meta'             => __( 'Term Meta', 'admin-page-framework-loader' ),
                'meta_boxes'            => __( 'Post Meta Boxes', 'admin-page-framework-loader' ),
                'page_meta_boxes'       => __( 'Page Meta Boxes', 'admin-page-framework-loader' ),
                'widgets'               => __( 'Widgets', 'admin-page-framework-loader' ),
                'user_meta'             => __( 'User Meta', 'admin-page-framework-loader' ),
                'utilities'             => __( 'Utilities', 'admin-page-framework-loader' ),
            );
        }

    /**
     * Validates the submitted form data.
     *
     * @since  3.5.4
     * @param  array $aInputs
     * @param  array $aOldInputs
     * @param  AdminPageFramework $oAdminPage
     * @param  array $aSubmitInfo
     * @return array
     */
    public function validate( $aInputs, $aOldInputs, $oAdminPage, $aSubmitInfo ) {

        $_bVerified = true;
        $_aErrors   = array();
        $aInputs    = $this->___getFieldValuesSanitized( $aInputs, $oAdminPage );

        // the class prefix must not contain white spaces and some other characters not supported in PHP class names.
        preg_match(
            '/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/',     // pattern - allowed characters for variables in PHP.
            $aInputs[ 'class_prefix' ],     // subject
            $_aMatches
        );
        if ( $aInputs[ 'class_prefix' ] && empty( $_aMatches ) ) {
            $_aErrors[ $this->sSectionID ][ 'class_prefix' ] = __( 'The prefix must consist of alphanumeric with underscores.', 'admin-page-framework-loader' );
            $_bVerified = false;
        }

        if ( ! $aInputs[ 'text_domain' ] ) {
            $_aErrors[ $this->sSectionID ][ 'text_domain' ] = __( 'The text domain cannot be empty.', 'admin-page-framework-loader' );
            $_bVerified = false;
        }

        // An invalid value is found. Set a field error array and an admin notice and return the old values.
        if ( ! $_bVerified ) {
            $oAdminPage->setFieldErrors( $_aErrors );
            $oAdminPage->setSettingNotice( __( 'There was something wrong with your input.', 'admin-page-framework-loader' ) );
            return $aOldInputs;
        }

        return $aInputs;

    }
        /**
         * Sanitizes user-submitted form field values.
         * @since  3.5.4
         * @param  array $aInputs
         * @param  AdminPageFramework $oAdminPage
         * @return array The modified input array.
         */
        private function ___getFieldValuesSanitized( array $aInputs, $oAdminPage ) {
            $aInputs[ 'class_prefix' ] = trim(
                $oAdminPage->oUtil->getElement(
                    $aInputs,
                    'class_prefix',
                    ''
                )
            );
            $aInputs[ 'text_domain' ] = trim(
                $oAdminPage->oUtil->getElement(
                    $aInputs,
                    'text_domain',
                    ''
                )
            );
            return $aInputs;
        }

    /**
     * Lets the user download their own version of Admin Page Framework.
     *
     * @since    3.5.4
     * @param    array              $aSavedData
     * @param    string             $sSubmittedFieldID
     * @param    string             $sSubmittedInputID
     * @param    AdminPageFramework $oAdminPage
     * @callback add_filter() export_{instantiated class name}_{section id}_{field id}
     */
    public function replyToDownloadFramework( $aSavedData, $sSubmittedFieldID, $sSubmittedInputID, $oAdminPage ) {

        $_sFrameworkDirPath = AdminPageFrameworkLoader_Registry::$sDirPath . '/library/apf';
        if ( ! file_exists( $_sFrameworkDirPath ) ) {
            return $aSavedData;
        }

        $_sTempFile = $oAdminPage->oUtil->setTempPath( 'admin-page-framework.zip' );
        $_sData     = $this->___getDownloadFrameworkZipFile(
            $_sFrameworkDirPath,
            $_sTempFile
        );
        header( "Content-Length: " . strlen( $_sData ) );
        unlink( $_sTempFile );
        return $_sData;

    }
        /**
         * Generates the framework zip data.
         *
         * @since  3.5.4
         * @return string The binary zip data.
         */
        private function ___getDownloadFrameworkZipFile( $sFrameworkDirPath, $sDestinationPath ) {

            $_oZip = new AdminPageFramework_Zip(
                $sFrameworkDirPath,
                $sDestinationPath,
                array(
                    'include_directory'             => false,   // wrap contents in a sub-directory
                    'additional_source_directories' => apply_filters(
                        AdminPageFrameworkLoader_Registry::HOOK_SLUG . '_filter_generator_additional_source_directories',
                        array() // directory paths
                    ),
                ),
                array(  // callbacks
                    'file_name'         => array( $this, 'replyToGetPathInArchiveModified' ),
                    'directory_name'    => array( $this, 'replyToGetPathInArchiveModified' ),
                    'file_contents'     => array( $this, 'replyToGetFileContentModified' ),
                )
            );
            $_bSucceed = $_oZip->compress();
            if ( ! $_bSucceed ) {
                return '';
            }
            return file_get_contents( $sDestinationPath );

        }
            /**
             * Modifies the path in the archive which include the file name.
             *
             * Return an empty string to drop the item.
             *
             * @remark Gets called earlier than the callback for file contents.
             * @param  string $sPathInArchive The internal path of the archive including the parsing file name.
             * @since  3.5.4
             * @return string
             */
            public function replyToGetPathInArchiveModified( $sPathInArchive ) {
                // Check if it belongs to selected components.
                if ( false === $this->___isAllowedArchivePath( $sPathInArchive ) ) {
                    return '';  // empty value will drop the entry
                }
                return $sPathInArchive;
            }
                /**
                 * Checks whether the passed archive path is allowed.
                 *
                 * @since  3.5.4
                 * @param  string  $sPath The path to check. It can be a directory or a file.
                 * @return boolean
                 */
                private function ___isAllowedArchivePath( $sPath ) {
                    foreach( $this->___getDisallowedArchiveDirectoryPaths() as $_sDisallowedPath ) {
                        $_bHasPrefix = $this->oFactory->oUtil->hasPrefix(
                            ltrim( $_sDisallowedPath, '/' ), // needle
                            ltrim( $sPath, '/' ) // haystack
                        );
                        if ( $_bHasPrefix ) {
                            return false;
                        }
                    }
                    return true;
                }
                /**
                 * Defines the archive paths of components.
                 *
                 * @remark      Make sure to have a trailing slash.
                 * Otherwise, 'factory/AdminPageFramework' will match items that belong to other components.
                 * @since       3.5.4
                 */
                private $_aComponentPaths = array(
                    'admin_pages'           => array(
                        'factory/admin_page/',
                    ),
                    'network_admin_pages'   => array(
                        'factory/admin_page/',
                        'factory/network_admin_page/',
                    ),
                    'post_types'            => array(
                        'factory/post_type/',
                    ),
                    'taxonomies'            => array(
                        'factory/taxonomy_field/',
                    ),
                    'term_meta'             => array(
                        'factory/taxonomy_field/',
                        'factory/term_meta/',
                    ),
                    'meta_boxes'            => array(
                        'factory/meta_box/',
                    ),
                    'page_meta_boxes'       => array(
                        'factory/meta_box/',
                        'factory/page_meta_box/',
                    ),
                    'widgets'               => array(
                        'factory/widget/',
                    ),
                    'user_meta'             => array(
                        'factory/user_meta/',
                    ),
                    'utilities'             => array(
                        'utility/',
                    ),
                );
                /**
                 * Returns an array holding allowed paths set to the archive.
                 * @since  3.5.4
                 * @return array
                 */
                private function ___getDisallowedArchiveDirectoryPaths() {

                    // Cache
                    static $_aDisallowedPaths;
                    if ( isset( $_aDisallowedPaths ) ) {
                        return $_aDisallowedPaths;
                    }

                    // User selected items
                    $_aSelectedComponents = $this->___getCheckedComponents();

                    // List paths.
                    $_aAllComponentsPaths       = array();
                    $_aSelectedComponentsPaths  = array();
                    foreach( $this->_aComponentPaths as $_sKey => $_aPaths ) {

                        // Extract all component paths.
                        $_aAllComponentsPaths = array_merge(
                            $_aAllComponentsPaths,
                            $_aPaths
                        );

                        // Extract selected components paths.
                        if ( in_array( $_sKey, $_aSelectedComponents, true ) ) {
                            $_aSelectedComponentsPaths = array_merge(
                                $_aSelectedComponentsPaths,
                                $_aPaths
                            );
                        }

                    }

                    return array_diff(
                        array_unique( $_aAllComponentsPaths ),
                        array_unique( $_aSelectedComponentsPaths )
                    );

                }
                    /**
                     * Returns an array holding elements that the user has selected in the form.
                     * @since  3.5.4
                     * @return array
                     */
                    private function ___getCheckedComponents() {
                        $_aChecked = $this->oFactory->oUtil->getElementAsArray(
                            $_POST,
                            array(
                                $this->oFactory->oProp->sOptionKey,
                                $this->sSectionID,
                                'components' // field id
                            ),
                            array()
                        );
                        $_aChecked = array_filter( $_aChecked );
                        return array_keys( $_aChecked );
                    }

            /**
             * Modifies the file contents of the archive.
             *
             * @since  3.5.4
             * @return string The modified file contents.
             */
            public function replyToGetFileContentModified( $sFileContent, $sPathInArchive, $sSourceItemPath ) {

                if ( ! $this->___isAllowedToModifyContent( $sPathInArchive, $sSourceItemPath ) ) {
                    return $sFileContent;
                }

                // Modify the file contents.
                $sFileContent = apply_filters(
                    AdminPageFrameworkLoader_Registry::HOOK_SLUG . '_filter_generator_file_contents',
                    $sFileContent,
                    $sPathInArchive,
                    $this->oFactory->oUtil->getElement(
                        $_POST,
                        array(
                            $this->oFactory->oProp->sOptionKey,
                        ),
                        array()
                    ),
                    $this->oFactory
                );

                // At this point, it is a php file.
                return $this->___getClassNameModifiedByPath( $sFileContent, $sPathInArchive );

            }
                /**
                 * @return boolean
                 * @since  3.9.0
                 */
                private function ___isAllowedToModifyContent( $sPathInArchive, $sSourceItemPath ) {

                    // Store ignore directory paths
                    static $_aIgnoreDirPaths = array();
                    $_sSourceItemDirPath = dirname( $sSourceItemPath );
                    if ( basename( $sSourceItemPath ) === 'ignore-apf-build.txt' ) {
                        $_aIgnoreDirPaths[] = $_sSourceItemDirPath;
                    }

                    // Check the file extension.
                    $_aAllowedExtensions = apply_filters(
                        AdminPageFrameworkLoader_Registry::HOOK_SLUG . '_filter_generator_allowed_file_extensions',
                        array( 'php', 'css', 'js' )
                    );
                    if ( ! in_array( pathinfo( $sPathInArchive, PATHINFO_EXTENSION ), $_aAllowedExtensions, true ) ) {
                        return false;
                    }

                    // Check if it is inside an ignored directory.
                    foreach( $_aIgnoreDirPaths as $_sIgnoreDirPath ) {
                        if ( false !== strpos( $sSourceItemPath, $_sIgnoreDirPath ) ) {
                            return false;
                        }
                    }
                    return true;

                }
                /**
                 * Modifies the given file contents.
                 *
                 * @since  3.5.4
                 * @return string
                 */
                private function ___getClassNameModifiedByPath( $sFileContent, $sPathInArchive ) {

                    // The inclusion class list file needs to be handled differently.
                    if ( $this->oFactory->oUtil->hasSuffix( 'admin-page-framework-class-map.php', $sPathInArchive ) ) {
                        return $this->___getClassNameOfIncludeListModified( $sFileContent );
                    }

                    // Insert notes in the header comment.
                    if ( $this->oFactory->oUtil->hasSuffix( 'admin-page-framework.php', $sPathInArchive ) ) {
                        $sFileContent = $this->___getFileDocblockModified( $sFileContent );
                        return $this->___getClassNameModified( $sFileContent );
                    }

                    $sFileContent = $this->___getClassNameModified( $sFileContent );

                    // If it is the message class, modify the text domain.
                    // @deprecated  3.6.0+
                    // if ( ! $this->oFactory->oUtil->hasSuffix( 'AdminPageFramework_Message.php', $sPathInArchive ) ) {
                        // return $sFileContent;
                    // }
                    return $this->___getTextDomainModified( $sFileContent );

                }
                    /**
                     * Inserts additional information such as an included component list and a date to the file doc-block (the header comment part).
                     * @since  3.5.4
                     * @return string
                     */
                    private function ___getFileDocblockModified( $sFileContent ) {

                        $_aCheckedComponents = $this->oFactory->oUtil->getArrayElementsByKeys(
                            $this->___getComponentLabels(),
                            $this->___getCheckedComponents()
                        );
                        $_aInsert            = array(
                            'Compiled on ' . date( 'Y-m-d' ),  // today's date
                            'Included Components: ' . implode( ', ', $_aCheckedComponents ),
                        );
                        $_sInsertComment     = apply_filters( AdminPageFrameworkLoader_Registry::HOOK_SLUG . '_filter_generator_header_comment', implode( PHP_EOL . ' * ', $_aInsert ) );
                        return preg_replace(
                            '#[^\S\r\n]+?\*/#', // needle - matches '*/' or ' */'
                            ' * ' . trim( $_sInsertComment ) . PHP_EOL . '\0', // replacement \0 is a back-reference to '*/'
                            $sFileContent, // subject
                            1 // replace only the first occurrence
                        );

                    }
                    /**
                     * Modifies the class inclusion list.
                     * @since       3.5.4
                     * @return      string
                     */
                    private function ___getClassNameOfIncludeListModified( $sFileContents ) {
                        // Replace the array key names.
                        $sFileContents = preg_replace_callback(
                            '/(["\'])(.+)\1(?=\s?+=>)/',  // pattern '
                            array( $this, '_replyToModifyPathName' ),   // callable
                            $sFileContents // subject
                        );
                        // Replace the registry class names.
                        return preg_replace_callback(
                            '/(=>\s?+)(.+)(?=::)/',  // pattern '
                            array( $this, '_replyToModifyPathName' ),   // callable
                            $sFileContents // subject
                        );
                    }
                        /**
                         * Modifies the regex-matched string.
                         * @callback    function        preg_replace_callback()
                         * @since       3.5.4
                         */
                        public function _replyToModifyPathName( $aMatches ) {
                            return $this->___getClassNameModified( $aMatches[ 0 ] );
                        }

                /**
                 * Modifies the given class name.
                 *
                 * @since  3.5.4
                 * @return string
                 */
                private function ___getClassNameModified( $sSubject ) {
                    $_sPrefix = $this->___getFormSubmitValueByFieldIDAsString( 'class_prefix' );
                    return strlen( $_sPrefix )
                        ? str_replace(
                            'AdminPageFramework', // search
                            $_sPrefix . 'AdminPageFramework', // replace
                            $sSubject // subject
                        )
                        : $sSubject;
                }
                
                /**
                 * Modifies the text domain in the given file contents.
                 *
                 * @since  3.5.4
                 * @return string
                 */
                private function ___getTextDomainModified( $sFileContents ) {
                    $_sTextDomain = $this->___getFormSubmitValueByFieldIDAsString( 'text_domain' );
                    return strlen( $_sTextDomain )
                        ? str_replace(
                            'admin-page-framework', // search
                            $_sTextDomain, // replace
                            $sFileContents // subject
                        )
                        : $sFileContents;
                }
                    /**
                     * Retrieves the value from the $_POST array by the given field ID.
                     *
                     * @since  3.5.4
                     * @return string
                     */
                    private function ___getFormSubmitValueByFieldIDAsString( $sFieldID ) {
                        static $_aCaches=array();
                        $_aCaches[ $sFieldID ] = isset( $_aCaches[ $sFieldID ] )
                            ? $_aCaches[ $sFieldID ]
                            : $this->oFactory->oUtil->getElement(
                                $_POST,
                                array(
                                    $this->oFactory->oProp->sOptionKey,
                                    $this->sSectionID,
                                    $sFieldID
                                ),
                                ''
                            );
                        return trim( ( string ) $_aCaches[ $sFieldID ] );
                    }

    /**
     * Modifies the HTTP header of the export field.
     *
     * @callback  add_filter()      export_header_{...}
     * @since     3.5.4
     * @return    array
     */
    public function replyToGetExportHTTPHeaderModified( $aHeader, $sFieldID, $sInputID, $mData, $sFileName, $oFactory ) {

        $sFileName = $this->___getDownloadFileName();
        return array(
            'Pragma'                    => 'public',
            'Expires'                   => 0,
            'Cache-Control'             => array(
                'must-revalidate, post-check=0, pre-check=0',
                'public',
            ),
            'Content-Description'       => 'File Transfer',
            'Content-type'              => 'application/octet-stream',   // 'application/zip' may work as well
            'Content-Transfer-Encoding' => 'binary',
            'Content-Disposition'       => 'attachment; filename="' . $sFileName .'";',
            // 'Content-Length'            => strlen( $mData ),
        ) + $aHeader;

    }

    /**
     * Filters the exporting file name.
     *
     * @callback add_filter()    "export_name_{page slug}_{tab slug}" filter.
     * @return   string
     */
    public function replyToFilterFileName( $sFileName, $sFieldID, $sInputID, $vExportingData, $oAdminPage ) {
        return $this->___getDownloadFileName();
    }

    /**
     * Returns the user-set file name.
     *
     * The user set text domain will be added as a prefix to `admin-page-framework.zip`.
     *
     * @since       3.5.4
     * @return      string
     */
    private function ___getDownloadFileName() {
        $_sFileNameWOExtension = $this->oFactory->oUtil->getElement(
            $_POST,
            array(
                $this->oFactory->oProp->sOptionKey,
                $this->sSectionID,
                'text_domain' // field id
            )
        );
        $_sFileNameWOExtension = trim( sanitize_text_field( $_sFileNameWOExtension ) );
        return $this->oFactory->oUtil->getAOrB(
                $_sFileNameWOExtension,
                $_sFileNameWOExtension . '-admin-page-framework',
                'admin-page-framework'
            )
            . '.' . AdminPageFramework_Registry::VERSION
            . '.zip';
    }

}