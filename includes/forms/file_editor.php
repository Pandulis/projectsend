<form action="upload-process-form.php" name="files" id="files" method="post" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?php echo getCsrfToken(); ?>" />

    <?php
        foreach($uploaded_files as $add_uploaded_field) {
    ?>
            <input type="hidden" name="file_ids[]" value="<?php echo $add_uploaded_field; ?>" />
    <?php
        }
    ?>

    <div class="container-fluid">
        <?php
            $i = 1;
            foreach ($uploaded_files as $file_id) {
                clearstatcache();
                $file = new ProjectSend\Classes\Files;
                $file->get($file_id);

                $location = UPLOADED_FILES_DIR.DS.$file->filename_on_disk;

                /**
                 * Check that the file is indeed present on the folder.
                 * If not, it is added to the failed files array.
                 */
                if (file_exists($location)) {
            ?>
                    <div class="file_editor <?php if ($i%2) { echo 'f_e_odd'; } ?>">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="file_number">
                                    <p><span class="glyphicon glyphicon-saved" aria-hidden="true"></span><?php echo $file->title; ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="row edit_files">
                            <div class="col-sm-12">
                                <div class="row edit_files_blocks">
                                    <div class="<?php echo (CURRENT_USER_LEVEL != 0 || CLIENTS_CAN_SET_EXPIRATION_DATE == '1' ) ? 'col-sm-6 col-md-3' : 'col-sm-12 col-md-12'; ?> column">
                                        <div class="file_data">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <h3><?php _e('File information', 'cftp_admin');?></h3>
                                                    <input type="hidden" name="file[<?php echo $i; ?>][original]" value="<?php echo $file->filename_original; ?>" />
                                                    <input type="hidden" name="file[<?php echo $i; ?>][file]" value="<?php echo $file->filename_on_disk; ?>" />

                                                    <div class="form-group">
                                                        <label><?php _e('Title', 'cftp_admin');?></label>
                                                        <input type="text" name="file[<?php echo $i; ?>][name]" value="<?php echo $file->title; ?>" class="form-control file_title" placeholder="<?php _e('Enter here the required file title.', 'cftp_admin');?>" />
                                                    </div>

                                                    <div class="form-group">
                                                        <label><?php _e('Description', 'cftp_admin');?></label>
                                                        <textarea name="file[<?php echo $i; ?>][description]" class="<?php if ( FILES_DESCRIPTIONS_USE_CKEDITOR == 1 ) { echo 'ckeditor'; } ?> form-control" placeholder="<?php _e('Optionally, enter here a description for the file.', 'cftp_admin');?>"><?php if (!empty($file->description)) { echo $file->description; } ?></textarea>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <?php
                                        /** The following options are available to users or client if clients_can_set_expiration_date set. */
                                        if (CURRENT_USER_LEVEL != 0 || CLIENTS_CAN_SET_EXPIRATION_DATE == '1' ) {
                                    ?>
                                        <div class="col-sm-6 col-md-3 column_even column">
                                            <div class="file_data">
                                                <?php
                                                    /**
                                                    * Only show the expiration options if the current
                                                    * uploader is a system user or client if clients_can_set_expiration_date is set.
                                                    */
                                                ?>
                                                <h3><?php _e('Expiration date', 'cftp_admin');?></h3>

                                                <div class="form-group">
                                                    <label for="file[<?php echo $i; ?>][expires_date]"><?php _e('Select a date', 'cftp_admin');?></label>
                                                        <div class="input-group date-container">
                                                            <input type="text" class="date-field form-control datapick-field" readonly id="file[<?php echo $i; ?>][expiry_date]" name="file[<?php echo $i; ?>][expiry_date]" value="<?php echo (!empty($file->expiry_date)) ? date('d-m-Y', strtotime($file->expiry_date)) : date('d-m-Y'); ?>" />
                                                            <div class="input-group-addon">
                                                                <i class="glyphicon glyphicon-time"></i>
                                                            </div>
                                                        </div>
                                                </div>

                                                <div class="checkbox">
                                                    <label for="exp_checkbox_<?php echo $i; ?>">
                                                        <input type="checkbox" name="file[<?php echo $i; ?>][expires]" id="exp_checkbox_<?php echo $i; ?>" value="1" <?php if ($file->expires) { ?>checked="checked"<?php } ?> /> <?php _e('File expires', 'cftp_admin');?>
                                                    </label>
                                                </div>

                                                <?php
                                                    /** The following options are available to users only */
                                                    if (CURRENT_USER_LEVEL != 0) {
                                                ?>

                                                    <div class="divider"></div>

                                                    <h3><?php _e('Public downloading', 'cftp_admin');?></h3>

                                                    <div class="checkbox">
                                                        <label for="pub_checkbox_<?php echo $i; ?>">
                                                            <input type="checkbox" id="pub_checkbox_<?php echo $i; ?>" name="file[<?php echo $i; ?>][public]" value="1" <?php if ($file->public) { ?>checked="checked"<?php } ?>/> <?php _e('Allow public downloading of this file.', 'cftp_admin');?>
                                                        </label>
                                                    </div>
                                            <?php
                                                } /** Close CURRENT_USER_LEVEL check */
                                            ?>
                                            </div>
                                        </div>
                                    <?php
                                        } /** Close CURRENT_USER_LEVEL check */
                                    ?>

                                    <?php
                                        /** The following options are available to users only */
                                        if (CURRENT_USER_LEVEL != 0) {
                                    ?>
                                            <div class="col-sm-6 col-md-3 assigns column">
                                                <div class="file_data">
                                                    <?php
                                                        /**
                                                        * Only show the CLIENTS select field if the current
                                                        * uploader is a system user, and not a client.
                                                        */
                                                    ?>
                                                    <h3><?php _e('Assignations', 'cftp_admin');?></h3>
                                                    <label><?php _e('Assign this file to', 'cftp_admin');?>:</label>
                                                    <select multiple="multiple" name="file[<?php echo $i; ?>][assignments][]" class="form-control chosen-select" data-placeholder="<?php _e('Select one or more options. Type to search.', 'cftp_admin');?>">
                                                        <optgroup label="<?php _e('Clients', 'cftp_admin');?>">
                                                            <?php
                                                                /**
                                                                 * The clients list is generated early on the file so the
                                                                 * array doesn't need to be made once on every file.
                                                                 */
                                                                foreach($clients as $client => $client_name) {
                                                                ?>
                                                                    <option value="<?php echo html_output('c'.$client); ?>"><?php echo html_output($client_name); ?></option>
                                                                <?php
                                                                }
                                                            ?>
                                                        </optgroup>
                                                        <optgroup label="<?php _e('Groups', 'cftp_admin');?>">
                                                            <?php
                                                                /**
                                                                 * The groups list is generated early on the file so the
                                                                 * array doesn't need to be made once on every file.
                                                                 */
                                                                foreach($groups as $group => $group_name) {
                                                                ?>
                                                                    <option value="<?php echo html_output('g'.$group); ?>"><?php echo html_output($group_name); ?></option>
                                                                <?php
                                                                }
                                                            ?>
                                                        </optgroup>
                                                    </select>
                                                    <div class="list_mass_members">
                                                        <a href="#" class="btn btn-xs btn-primary add-all" data-type="assigns"><?php _e('Add all','cftp_admin'); ?></a>
                                                        <a href="#" class="btn btn-xs btn-primary remove-all" data-type="assigns"><?php _e('Remove all','cftp_admin'); ?></a>
                                                        <a href="#" class="btn btn-xs btn-danger copy-all" data-type="assigns"><?php _e('Copy selections to other files','cftp_admin'); ?></a>
                                                    </div>

                                                    <div class="divider"></div>

                                                    <div class="checkbox">
                                                        <label for="hid_checkbox_<?php echo $i; ?>">
                                                            <input type="checkbox" id="hid_checkbox_<?php echo $i; ?>" name="file[<?php echo $i; ?>][hidden]" value="1" /> <?php _e('Upload hidden (will not send notifications)', 'cftp_admin');?>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-sm-6 col-md-3 categories column">
                                                <div class="file_data">
                                                    <h3><?php _e('Categories', 'cftp_admin');?></h3>
                                                    <label><?php _e('Add to', 'cftp_admin');?>:</label>
                                                    <select multiple="multiple" name="file[<?php echo $i; ?>][categories][]" class="form-control chosen-select" data-placeholder="<?php _e('Select one or more options. Type to search.', 'cftp_admin');?>">
                                                        <?php
                                                            /**
                                                             * The categories list is generated early on the file so the
                                                             * array doesn't need to be made once on every file.
                                                             */
                                                            echo generate_categories_options( $get_categories['arranged'], 0 );
                                                        ?>
                                                    </select>
                                                    <div class="list_mass_members">
                                                        <a href="#" class="btn btn-xs btn-primary add-all" data-type="categories"><?php _e('Add all','cftp_admin'); ?></a>
                                                        <a href="#" class="btn btn-xs btn-primary remove-all" data-type="categories"><?php _e('Remove all','cftp_admin'); ?></a>
                                                        <a href="#" class="btn btn-xs btn-danger copy-all" data-type="categories"><?php _e('Copy selections to other files','cftp_admin'); ?></a>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php
                                            } /** Close CURRENT_USER_LEVEL check */
                                        ?>
                                </div>
                            </div>
                        </div>
                    </div>
            <?php
                    $i++;
                }
                else {
                    $upload_failed[] = $file;
                }
            }
        ?>

    </div> <!-- container -->

    <?php
        /**
         * Take the list of failed files and store them as a text string
         * that will be passed on a hidden field when posting the form.
         */
        $upload_failed = array_filter($upload_failed);
        $upload_failed_hidden = implode(',',$upload_failed);
    ?>
    <input type="hidden" name="upload_failed" value="<?php echo $upload_failed_hidden; ?>" />

    <div class="after_form_buttons">
        <button type="submit" name="submit" class="btn btn-wide btn-primary" id="upload-continue"><?php _e('Save','cftp_admin'); ?></button>
    </div>
</form>