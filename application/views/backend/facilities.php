<!DOCTYPE html>
<html>

<head>
    <title>
        <?php echo "Facilities" ?></title>

    <script>
        function refreshme() {
            top.restoreSession();
            document.location.reload();
        }
        $(function() {

            $(".medium_modal").on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                dlgopen('', '', 700, 590, '', '', {
                    allowResize: false,
                    allowDrag: true, // note these default to true if not defined here. left as example.
                    type: 'iframe',
                    url: $(this).attr('href')
                });
            });

            $(".addfac_modal").on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                // $('#myModal').modal('show').find('.modal-content').load($(this).attr('href'));
            });

        });
    </script>


</head>

<body>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="clearfix">
                    <h2 class="clearfix"><?php echo "Facilities"; ?></h2>
                </div>
                <a href="" class=" btn btn-primary btn-add" data-toggle="modal" data-target="#facilityModal">
                    <i class=" far fa fa-plus" aria-hidden="true"></i>
                    <?php echo ('Add Facility'); ?>
                </a>
            </div>
        </div>
        <br />

        <div class="row">
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead class="fc-thead">
                            <tr>
                                <th><?php echo 'Name'; ?></th>
                                <th><?php echo 'Billing Address'; ?></th>
                                <th><?php echo 'Mailing Address'; ?></th>
                                <th><?php echo 'Phone'; ?></th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td>John</td>
                                <td>665 Roadsby Road,Longview,FL,USA 333222 </td>
                                <td>Miguel Negrete num 133, col guadalupana, Santa Maria del Río S.L.P.,SANTA MARIA DEL RIO SLP,SLP,MEXICO 79516 </td>
                                <td>86 137 1123</td>
                            </tr>
                            <tr>
                                <td>Mary</td>
                                <td>Miguel Negrete num 133, col guadalupana, Santa Maria del Río S.L.P.,SANTA MARIA DEL RIO SLP,SLP,MEXICO 79516</td>
                                <td>665 Roadsby Road,Longview,FL,USA 333222 </td>
                                <td>86 137 1123</td>
                            </tr>
                            <tr>
                                <td>July</td>
                                <td>Miguel Negrete num 133, col guadalupana, Santa Maria del Río S.L.P.,SANTA MARIA DEL RIO SLP,SLP,MEXICO 79516</td>
                                <td>65 Roadsby Road,Longview,FL,USA 333222</td>
                                <td>86 137 1123</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>


        </div>
    </div>

    <!--Facilites Add Model -->
    <div id="facilityModal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header ">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Add Facility</h4>
                </div>
                <div class="modal-body">
                    <div class="py-3">
                        <div class="btn-group">
                            <button class="btn btn-primary btn-save" name='form_save' id='form_save' onclick="submitform();"> <i class="fa fa-check" aria-hidden="true"></i> <?php echo ('Save'); ?></button>
                            <button class="btn btn-secondary btn-cancel" data-dismiss="modal"> <i class="fa fa-times" aria-hidden="true"></i> <?php echo ('Cancel'); ?></button>
                        </div>
                    </div>
                    <form name='facility-add' id='facility-add' method='post' action="facilities.php">

                        <input type="hidden" name="mode" value="facility" />

                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="facility"><?php echo ('Name'); ?>: * </label>
                                    <input class="form-control" type="text" name="facility" size="20" value="" required />
                                </div>
                                <div class="form-group">
                                    <label for="street"><?php echo ('Address'); ?>:</label>
                                    <input class="form-control" type="text" size="20" name="street" value="" />
                                </div>
                                <div class="form-group">
                                    <label for="city"><?php echo ('City'); ?>:</label>
                                    <input class="form-control" type="text" size="20" name="city" value="" />
                                </div>
                                <div class="form-group">
                                    <label for="state"><?php echo ('State'); ?>:</label>
                                    <input class="form-control" type="text" size="20" name="state" value="" />
                                </div>
                                <div class="form-group">
                                    <label for="country_code"><?php echo ('Country'); ?>:</label>
                                    <input class="form-control" type="text" size="20" name="country_code" value="" />
                                </div>
                                <div class="form-group">
                                    <label for="website"><?php echo ('Website'); ?>: </label>
                                    <input class="form-control" type="text" size="20" name="website" value="" />
                                </div>
                                <div class="form-group">
                                    <label for="iban"><?php echo ('IBAN'); ?>: </label>
                                    <input class="form-control" type="text" size="20" name="iban" value="" />
                                </div>
                                <div class="form-row custom-control custom-switch my-2">
                                    <div class="col">
                                        <input type="checkbox" class='custom-control-input' name="billing_location" id="billing_location" value="1" />
                                        <label for="billing_location" class='custom-control-label'><?php echo ('Billing Location'); ?></label>
                                    </div>
                                </div>
                                <div class="form-row custom-control custom-switch my-2">
                                    <div class="col">
                                        <input type="checkbox" class='custom-control-input' name="accepts_assignment" id="accepts_assignment" value="1" aria-describedby="assignmentHelp">
                                        <label for="accepts_assignment" class='custom-control-label'><?php echo ('Accepts Assignment'); ?></label>
                                    </div>
                                    <div class="col">
                                        <small id="assignmentHelp" class="text-muted">
                                            (<?php echo ('only if billing location'); ?>)
                                        </small>
                                    </div>
                                </div>
                                <div class="form-row custom-control custom-switch my-2">
                                    <div class="col">
                                        <input type="checkbox" class='custom-control-input' name="service_location" id="service_location" value="1" />
                                        <label for="service_location" class='custom-control-label'><?php echo ('Service Location'); ?></label>
                                    </div>
                                </div>
                                <div class="form-row custom-control custom-switch my-2">
                                    <div class="col">
                                        <?php
                                        // $disabled = '';
                                        // $resPBE = $facilityService->getPrimaryBusinessEntity(array("excludedId" => ($my_fid ?? null)));
                                        // if (!empty($resPBE) && sizeof($resPBE) > 0) {
                                        //     $disabled = 'disabled';
                                        // }
                                        ?>
                                        <input type='checkbox' class='custom-control-input' name='primary_business_entity' id='primary_business_entity' value='1' <?php echo (!empty($facility['primary_business_entity']) && ($facility['primary_business_entity'] == 1)) ? 'checked' : ''; ?> <?php if ($GLOBALS['erx_enable']) { ?> onchange='return displayAlert()' <?php } ?> <?php echo $disabled; ?>>
                                        <label for="primary_business_entity" class='custom-control-label'><?php echo ('Primary Business Entity'); ?></label>

                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="ncolor"><?php echo ('Color'); ?>: </label>
                                    <input class="form-control" type="text" name="ncolor" id="ncolor" size="20" value="" />
                                    <span>[<a href="javascript:void(0);" onClick="pick('pick','newcolor');return false;" NAME="pick" ID="pick"><?php echo ('Pick'); ?></a>]</span>
                                </div>
                                <div class="form-group">
                                    <label for="pos_code"><?php echo ('POS Code'); ?>: </label>
                                    <select class="form-control" name="pos_code">
                                        <?php
                                        // $pc = new POSRef();

                                        // foreach ($pc->get_pos_ref() as $pos) {
                                        //     echo "<option value=\"" . attr($pos["code"]) . "\" ";
                                        //     echo ">" . text($pos['code'])  . ": " . text($pos['title']);
                                        //     echo "</option>\n";
                                        // }

                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="domain_identifier"><?php echo ('CLIA Number'); ?>:</label>
                                    <input class="form-control" type="text" name="domain_identifier" size="45" />
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="phone"><?php echo ('Phone'); ?>:</label>
                                    <input class="form-control" type="text" name="phone" size="20" value="" />
                                </div>
                                <div class="form-group">
                                    <label for="fax"><?php echo ('Fax'); ?>:</label>
                                    <input class="form-control" type="text" name="fax" size="20" value="" />
                                </div>
                                <div class="form-group">
                                    <label for="postal_code"><?php echo ('Zip Code'); ?>:</label>
                                    <input class="form-control" type="text" size="20" name="postal_code" value="" />
                                </div>
                                <div class="form-group">
                                    <label for="tax_id_type"><?php echo ('Tax ID'); ?>:</label>
                                    <span class="form-inline">
                                        <select class="form-control" name="tax_id_type">
                                            <option value="EI"><?php echo ('EIN'); ?></option>
                                            <option value="SY"><?php echo ('SSN'); ?></option>
                                        </select>
                                        <input class="form-control" type="text" size="11" name="federal_ein" value="" />
                                    </span>
                                </div>
                                <div class="form-group">
                                    <label for="facility_taxonomy"><?php echo ('Facility NPI'); ?>:</label>
                                    <input class="form-control" type="text" size="20" name="facility_npi" value="" />
                                </div>
                                <div class="form-group">
                                    <label for="facility_taxonomy"><?php echo ('Facility Taxonomy'); ?>:</label>
                                    <input class="form-control" type="text" size="20" name="facility_taxonomy" value="" />
                                </div>
                                <div class="form-group">
                                    <label for="email"><?php echo ('Email'); ?>: </label>
                                    <input class="form-control" type="text" size="20" name="email" value="" />
                                </div>
                                <div class="form-group">
                                    <label for="attn"><?php echo ('Billing Attn'); ?>:</label>
                                    <input class="form-control" type="text" name="attn" size="45" />
                                </div>
                                <div class="form-group">
                                    <label for="facility_id"><?php echo ('Facility ID'); ?>:</label>
                                    <input class="form-control" type="text" name="facility_id" size="20" />
                                </div>
                                <div class="form-group">
                                    <label for="oid"><?php echo ('OID'); ?>: </label>
                                    <input class="form-control" type="text" size="20" name="oid" value="" />
                                </div>

                            </div>
                        </div>
                        <hr />
                        <div class="form-group">
                            <label for="mail_stret"><?php echo ('Mailing Address'); ?>: </label>
                            <input class="form-control" type="text" size="20" name="mail_street" value="" />
                        </div>
                        <div class="form-group">
                            <label for="mail_street2"><?php echo ('Dept'); ?>: </label>
                            <input class="form-control" type="text" size="20" name="mail_street2" value="" />
                        </div>
                        <div class="form-group">
                            <label for="mail_city"><?php echo ('City'); ?>: </label>
                            <input class="form-control" type="text" size="20" name="mail_city" value="" />
                        </div>
                        <div class="form-group">
                            <label for="mail_state"><?php echo ('State'); ?>: </label>
                            <input class="form-control" type="text" size="20" name="mail_state" value="" />
                        </div>
                        <div class="form-group">
                            <label for="mail_zip"><?php echo ('Zip'); ?>: </label>
                            <input class="form-control" type="text" size="20" name="mail_zip" value="" />
                        </div>
                        <div class="form-group">
                            <label for="info"><?php echo ('Info'); ?>: </label>
                            <textarea class="form-control" size="20" name="info"></textarea>
                        </div>
                        <div>
                            <p class=""><span class="mandatory">*</span> <?php echo ('Required'); ?></p>
                        </div>
                    </form>
                    <div class="py-3">
                        <div class="btn-group">
                            <button class="btn btn-primary " name='form_save' id='form_save' onclick="submitform();"> <i class="fa fa-check" aria-hidden="true"></i> <?php echo ('Save'); ?></button>
                            <button class="btn btn-secondary" data-dismiss="modal"> <i class="fa fa-times" aria-hidden="true"></i> <?php echo ('Cancel'); ?></button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


</body>

</html>