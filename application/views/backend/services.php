<link rel="stylesheet" type="text/css" href="<?= asset_url('/assets/ext/cropper/cropper.min.css') ?>">

<script src="<?= asset_url('assets/js/backend_services_helper.js') ?>"></script>
<script src="<?= asset_url('assets/js/backend_categories_helper.js') ?>"></script>
<script src="<?= asset_url('assets/js/backend_categories_template_helper.js') ?>"></script>
<script src="<?= asset_url('assets/js/backend_services.js') ?>"></script>

<script>
    var GlobalVariables = {
        csrfToken: <?= json_encode($this->security->get_csrf_hash()) ?>,
        baseUrl: <?= json_encode($base_url) ?>,
        dateFormat: <?= json_encode($date_format) ?>,
        timeFormat: <?= json_encode($time_format) ?>,
        services: <?= json_encode($services) ?>,
        categories: <?= json_encode($categories) ?>,
        timezones: <?= json_encode($timezones) ?>,
        user: {
            id: <?= $user_id ?>,
            email: <?= json_encode($user_email) ?>,
            timezone: <?= json_encode($timezone) ?>,
            role_slug: <?= json_encode($role_slug) ?>,
            privileges: <?= json_encode($privileges) ?>
        }
    };

    $(function() {
        BackendServices.initialize(true);
    });
</script>


<script src="<?= asset_url('assets/ext/cropper/cropper.min.js') ?>"></script>

<div class="container-fluid backend-page" id="services-page">
    <ul class="nav nav-pills">
        <li class="nav-item">
            <a class="nav-link active" href="#services" data-toggle="tab">
                <?= lang('services') ?>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#categories" data-toggle="tab">
                <?= lang('categories') ?>
            </a>
        </li>

    </ul>

    <div class="tab-content">

        <!-- SERVICES TAB -->

        <div class="tab-pane active" id="services">
            <div class="row">
                <div id="filter-services" class="filter-records col col-12 col-md-5">
                    <form class="mb-4">
                        <div class="input-group">
                            <input type="text" class="key form-control">

                            <div class="input-group-addon">
                                <div>
                                    <button class="filter btn btn-outline-secondary" type="submit" data-tippy-content="<?= lang('filter') ?>">
                                        <i class="fas fa-search"></i>
                                    </button>
                                    <button class="clear btn btn-outline-secondary" type="button" data-tippy-content="<?= lang('clear') ?>">
                                    <i class="fas fa-redo-alt"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <h3><?= lang('services') ?></h3>
                    <div class="results"></div>
                </div>

                <div class="record-details column col-12 col-md-5">
                    <div class="btn-toolbar mb-4">
                        <div class="add-edit-delete-group btn-group">
                            <button id="add-service" class="btn btn-primary">
                                <i class="far fa-plus-square mr-2"></i>
                                <?= lang('add') ?>
                            </button>
                            <button id="edit-service" class="btn btn-outline-secondary" disabled="disabled">
                                <i class="far fa-edit mr-2"></i>
                                <?= lang('edit') ?>
                            </button>
                            <button id="delete-service" class="btn btn-outline-secondary" disabled="disabled">
                                <i class="far fa-trash-alt mr-2"></i>
                                <?= lang('delete') ?>
                            </button>
                        </div>

                        <div class="save-cancel-group btn-group" style="display:none;">
                            <button id="save-service" class="btn btn-primary">
                                <i class="far fa-check-square mr-2"></i>
                                <?= lang('save') ?>
                            </button>
                            <button id="cancel-service" class="btn btn-outline-secondary">
                                <i class="fas fa-ban mr-2"></i>
                                <?= lang('cancel') ?>
                            </button>
                        </div>
                    </div>

                    <h3><?= lang('details') ?></h3>

                    <div class="form-message alert" style="display:none;"></div>

                    <input type="hidden" id="service-id">

                    <div class="form-group">
                        <label for="service-name">
                            <?= lang('name') ?>
                            <span class="text-danger">*</span>
                        </label>
                        <input id="service-name" class="form-control required" maxlength="128">
                    </div>

                    <div class="form-group">
                        <label for="service-duration">
                            <?= lang('duration_minutes') ?>
                            <span class="text-danger">*</span>
                        </label>
                        <input id="service-duration" class="form-control required" type="number" min="15">
                    </div>

                    <div class="form-group">
                        <label for="service-price">
                            <?= lang('price') ?>
                            <span class="text-danger">*</span>
                        </label>
                        <input id="service-price" class="form-control required">
                    </div>

                    <div class="form-group">
                        <label for="service-currency">
                            <?= lang('currency') ?>

                        </label>
                        <input id="service-currency" class="form-control" maxlength="32">
                    </div>

                    <div class="form-group">
                        <label for="service-category">
                            <?= lang('category') ?>
                        </label>
                        <select id="service-category" class="form-control"></select>
                    </div>

                    <div class="form-group" style="display:none;">
                        <label for="service-availabilities-type">
                            <?= lang('availabilities_type') ?>
                        </label>
                        <select id="service-availabilities-type" class="form-control">
                            <option value="<?= AVAILABILITIES_TYPE_FLEXIBLE ?>">
                                <?= lang('flexible') ?>
                            </option>
                            <option value="<?= AVAILABILITIES_TYPE_FIXED ?>">
                                <?= lang('fixed') ?>
                            </option>
                        </select>
                    </div>

                    <div class="form-group" style="display:none;">
                        <label for="service-attendants-number">
                            <?= lang('attendants_number') ?>
                            <span class="text-danger">*</span>
                        </label>
                        <input id="service-attendants-number" class="form-control required" type="number" min="1">
                    </div>

                    <div class="form-group">
                        <label for="service-location">
                            <?= lang('location') ?>

                        </label>
                        <input id="service-location" class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="service-description">
                            <?= lang('description') ?>
                        </label>
                        <textarea id="service-description" rows="4" class="form-control"></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- CATEGORIES TAB -->

        <div class="tab-pane" id="categories">
            <div class="row">
                <div id="filter-categories" class="filter-records column col-12 col-md-5">
                    <form class="input-append mb-4">
                        <div class="input-group">
                            <input type="text" class="key form-control">

                            <div class="input-group-addon">
                                <div>
                                    <button class="filter btn btn-outline-secondary" type="submit" data-tippy-content="<?= lang('filter') ?>">
                                        <i class="fas fa-search"></i>
                                    </button>
                                    <button class="clear btn btn-outline-secondary" type="button" data-tippy-content="<?= lang('clear') ?>">
                                        <i class="fas fa-redo-alt"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <h3><?= lang('categories') ?></h3>
                    <div class="results"></div>
                </div>

                <div class="record-details col-12 col-md-5">
                    <div class="btn-toolbar mb-4">
                        <div class="add-edit-delete-group btn-group">
                            <button id="add-category" class="btn btn-primary">
                                <i class="far fa-plus-square mr-2"></i>
                                <?= lang('add') ?>
                            </button>
                            <button id="edit-category" class="btn btn-outline-secondary" disabled="disabled">
                                <i class="far fa-edit mr-2"></i>
                                <?= lang('edit') ?>
                            </button>
                            <button id="delete-category" class="btn btn-outline-secondary" disabled="disabled">
                                <i class="far fa-trash-alt mr-2"></i>
                                <?= lang('delete') ?>
                            </button>
                        </div>

                        <div class="save-cancel-group btn-group" style="display:none;">
                            <button id="save-category" class="btn btn-primary">
                                <i class="far fa-check-square mr-2"></i>
                                <?= lang('save') ?>
                            </button>
                            <button id="cancel-category" class="btn btn-outline-secondary">
                                <i class="fas fa-ban mr-2"></i>
                                <?= lang('cancel') ?>
                            </button>
                        </div>
                    </div>

                    <h3><?= lang('details') ?></h3>

                    <div class="form-message alert" style="display:none;"></div>

                    <input type="hidden" id="category-id">
                    <input type="hidden" id="category-image">

                    <div class="btn-group">
                            <button id="insert-categoriestemplate" class="btn btn-primary" disabled="disabled"  style="display:none;">
                                <i class="far fa-edit mr-2"></i>
                                <?= lang('service_category_template_select') ?>
                            </button>
                    </div>

                    <div class="row">
                        <div class="col-md-4">&nbsp;</div>
                            <div class="col-md-4">
                                <div class="image_area" >
                                    <form method="post">
                                        <label for="upload_image">
                                            <div class="img_wrapper">
                                                <img src="./../../assets/img/user.svg" id="uploaded_image" class="img-responsive img-circle rounded-circle"  />
                                            </div>
                                            <div class="overlay">
                                                <div class="text">Click to Edit Image</div>
                                            </div>
                                            <input type="file" accept=".jpg, .jpeg, .png" name="image" class="image" id="upload_image" style="display:none" />
                                        </label>
                                    </form>
                                </div>
                            </div>
                            <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true" >
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Crop Image Before Upload</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">×</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="img-container">
                                                <div class="row">
                                                    <div class="col-md-8">
                                                        <img src="" id="sample_image" />
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="preview"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" id="crop" class="btn btn-primary">Crop</button>
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="alert alert-danger alert-dismissible" style="display:none; position:relative; margin:auto" id="passwordsNoMatchRegister" role="alert">
                                Please choose an  <strong>image</strong> file.
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                 <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                    </div>

                    <div class="form-group">
                        <label for="category-name">
                            <?= lang('name') ?>
                            <span class="text-danger">*</span>
                        </label>
                        <input id="category-name" class="form-control required">
                    </div>

                    <div class="form-group">
                        <label for="category-description">
                            <?= lang('description') ?>

                        </label>
                        <textarea id="category-description" rows="4" class="form-control"></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- categories template TAB -->
        <div class="tab-pane" id="categories_template" style="min-height:400px; height:auto!important; height:400px;">
            <div class="row">
                <div id="filter-categoriesTemplate" class="filter-records column col-12 col-md-5">
                    <form class="input-append mb-4">
                        <div class="input-group">
                            <input type="text" class="key form-control">

                            <div class="input-group-addon">
                                <div>
                                    <button class="filter btn btn-outline-secondary" type="submit" data-tippy-content="<?= lang('filter') ?>">
                                        <i class="fas fa-search"></i>
                                    </button>
                                    <button class="clear btn btn-outline-secondary" type="button" data-tippy-content="<?= lang('clear') ?>">
                                        <i class="fas fa-redo-alt"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <h3><?= lang('categories_template') ?></h3>
                    <div class="results"></div>
                </div>

                <div class="record-details col-12 col-md-5">
                   
                    <div class="btn-toolbar mb-4">
                        <div class="add-edit-delete-group btn-group">
                            <button id="add-categoryTemplate" class="btn btn-primary">
                                <i class="far fa-plus-square mr-2"></i>
                                <?= lang('add') ?>
                            </button>
                            <button id="edit-categoryTemplate" class="btn btn-outline-secondary" disabled="disabled">
                                <i class="far fa-edit mr-2"></i>
                                <?= lang('edit') ?>
                            </button>
                            <button id="delete-categoryTemplate" class="btn btn-outline-secondary" disabled="disabled">
                                <i class="far fa-trash-alt mr-2"></i>
                                <?= lang('delete') ?>
                            </button>
                        </div>

                        <div class="save-cancel-group btn-group" style="display:none;">
                            <button id="save-categoryTemplate" class="btn btn-primary">
                                <i class="far fa-check-square mr-2"></i>
                                <?= lang('save') ?>
                            </button>
                            <button id="cancel-categoryTemplate" class="btn btn-outline-secondary">
                                <i class="fas fa-ban mr-2"></i>
                                <?= lang('cancel') ?>
                            </button>
                        </div>
                    </div>
                      <!--
                    <div style="margin-top:62.5px;"> </div>
                     -->
                    <h3><?= lang('details') ?></h3>

                    <div class="form-message alert" style="display:none;"></div>

                    <input type="hidden" id="categoryTemplate-id">
                     <div class="form-group">
                        <label for="ccategoryTemplate-image">
                            <?= lang('image') ?>
                        </label>
                        <img id="categoryTemplate-image" class="img-responsive" style="width: 220px;height: 220px;">
                    </div>
                    <div class="form-group">
                        <label for="ccategoryTemplate-name">
                            <?= lang('name') ?>
                            <span class="text-danger">*</span>
                        </label>
                        <input id="categoryTemplate-name" class="form-control required">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MANAGE Categories Template MODAL -->
<div id="manage-categoriestemplate"  class="modal fade manage-categoriestemplate" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title"><?= lang('categories_template') ?></h3>
                <button class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="modal-message alert d-none"></div>
                <table border="1" cellspacing="0" cellpadding="0"  width="95%" style="text-align: center;" >
                </table>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline-secondary" data-dismiss="modal">
                    <i class="fas fa-ban"></i>
                    <?= lang('cancel') ?>
                </button>
                <button id="confirm-categoriestemplate" class="btn btn-primary">
                    <i class="far fa-check-square mr-2"></i>
                    <?= lang('confirm') ?>
                </button>
            </div>
        </div>
    </div>
</div>

<script>

$(document).ready(function(){
   //alert('okay')
	var $modal = $('#modal');

	var image = document.getElementById('sample_image');

	var cropper;

	$('#upload_image').change(function(event){
		var files = event.target.files;
        console.log(files);

		var done = function(url){
			image.src = url;
			$modal.modal('show');
		};

		if(files && files.length > 0)
		{
            if (/^image\/\w+$/.test(files[0].type)) {
                reader = new FileReader();
                reader.onload = function(event)
                {
                    done(reader.result);
                };
                reader.readAsDataURL(files[0]);
            } else {
                //alert('Please choose an image file.');
                $('#passwordsNoMatchRegister').show();
            }
		}
	});

	$modal.on('shown.bs.modal', function() {
		cropper = new Cropper(image, {
			aspectRatio: 1,
			viewMode: 3,
			preview:'.preview'
		});
	}).on('hidden.bs.modal', function(){
		cropper.destroy();
   		cropper = null;
	});

	$('#crop').click(function(){
		canvas = cropper.getCroppedCanvas({
			width:400,
			height:400
		});

		canvas.toBlob(function(blob){
			url = URL.createObjectURL(blob);
			var reader = new FileReader();
			reader.readAsDataURL(blob);
			reader.onloadend = function(){
				var base64data = reader.result;
                var data = base64data
                $modal.modal('hide');
				$('#uploaded_image').attr('src', data);

				// $.ajax({
				// 	url:'upload.php',
				// 	method:'POST',
				// 	data:{image:base64data},
				// 	success:function(data)
				// 	{
				// 		$modal.modal('hide');
				// 		$('#uploaded_image').attr('src', data);
				// 	}
				// });
			};
		});
	});
	
});
</script>
