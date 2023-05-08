
                </main>
            </div>
        <footer class="app-footer">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12 text-center">
                        <?php
                        $f_ant = 2020;
                        $f_act = date("Y");
                        if((int)$f_ant == (int)$f_act){ $fecha = $f_act; }
                        else if((int)$f_act > (int)$f_ant){ $fecha = $f_ant." - ".$f_act; }
                        ?>
                        IMC &copy; <?=$fecha?> All Rights Reserved.
                    </div>
                    <div class="col-4 d-none">
                        <a href="#" class="float-right back-top cursor-pointer">
                            <i class=" ti-arrow-circle-up"></i>
                        </a>
                    </div>
                </div>
            </div>
        </footer>

                <link href = "https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.10.4/themes/black-tie/jquery-ui.min.css" rel = "stylesheet">
                <script src = "https://cdnjs.cloudflare.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
                <script src="https://code.jquery.com/jquery-latest.min.js"></script>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.1/js/bootstrap.min.js"></script>
                <script src="../assets/plugins/lobicard/js/lobicard.js"></script>
                <!--  Menu Accordion -->
                <script src="https://cdn.jsdelivr.net/npm/dcjqaccordion@2.7.1/js/jquery.cookie.js"></script>
                <script src="https://cdn.jsdelivr.net/npm/dcjqaccordion@2.7.1/js/jquery.dcjqaccordion.2.7.min.js"></script>
                <!--  Top Scroll -->
                <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-scrollTo/1.4.6/jquery.scrollTo.min.js"></script>
                <!--  sweetalert -->
                <link href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.25.0/sweetalert2.min.css" rel="stylesheet" type="text/css">
                <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.25.0/sweetalert2.min.js"></script>
                <!--  blockUI -->
                <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.blockUI/2.70/jquery.blockUI.min.js"></script>
                <!-- DataTable -->
                <link rel="stylesheet" type="text/css" href="../assets/css/components.min.css<?=$version?>">
                <script src="https://cdn.datatables.net/v/dt/dt-1.10.16/sl-1.2.5/datatables.min.js"></script>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables-responsive/2.1.0/dataTables.responsive.min.js"></script>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables-buttons/2.1.0/js/dataTables.buttons.min.js"></script>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables.net-fixedcolumns/4.1.0/dataTables.fixedColumns.min.js"></script>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables-buttons/2.1.0/js/buttons.html5.min.js"></script>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables-buttons/2.1.0/js/buttons.colVis.min.js"></script>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.3.0-beta.1/pdfmake.min.js"></script>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.3.0-beta.1/vfs_fonts.min.js"></script>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.7.1/jszip.min.js"></script>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-footable/2.0.1.4/js/footable.min.js"></script>
                <link href="https://gyrocode.github.io/jquery-datatables-checkboxes/1.2.11/css/dataTables.checkboxes.css" rel="stylesheet" />
                <script src="https://gyrocode.github.io/jquery-datatables-checkboxes/1.2.11/js/dataTables.checkboxes.min.js"></script>
                <!--select2-->
                <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet">
                <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.full.min.js"></script>

                <!--init scripts-->
                <script src="../assets/js/main.js<?=$version?>"></script>
                <script src="../assets/js/site.js<?=$version?>"></script>
                <script src="../assets/js/sistema.js<?=$version?>"></script>

    </body>
</html>