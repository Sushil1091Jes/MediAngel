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
                <a href="" class=" btn btn-primary btn-add" data-toggle="modal" data-target="#myModal">
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
    <div id="myModal" class="modal fade text-center">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Modal Header</h4>
                </div>
                <div class="modal-body">
                    <p>Some text in the modal.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


</body>

</html>