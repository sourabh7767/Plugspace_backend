<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ config('app.name') }}</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <link rel="shortcut icon" href="{{ env('PUBLIC_PATH')}}images/logo.png" />
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"
        />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap4-toggle/3.6.1/bootstrap4-toggle.min.css"/>

    <!-- AdminLTE -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.1.0/css/adminlte.min.css" />

    <!-- iCheck -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/icheck-bootstrap/3.0.1/icheck-bootstrap.min.css" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css"/>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css"/>
    <link rel="stylesheet" href="{{env('PUBLIC_PATH').'css/custom.css'}}">
    <link rel="stylesheet" href="{{env('PUBLIC_PATH').'css/AdminLTE.min.css'}}">
    <link rel="stylesheet" href="{{env('PUBLIC_PATH').'css/dataTables.bootstrap.min.css'}}">
    <link rel="stylesheet" href="{{env('PUBLIC_PATH').'css/jquery.dataTables.min.css'}}">
    @stack('third_party_stylesheets')

    @stack('page_css')
</head>

<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
    <!-- Main Header -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
        </ul>

        <ul class="navbar-nav ml-auto">
            <li class="nav-item dropdown user-menu">
                <a href="javascript:;" class="nav-link dropdown-toggle" data-toggle="dropdown">
                    <img src="{{ env('PUBLIC_PATH')}}images/adminlogo.svg"
                         class="user-image img-circle elevation-2" alt="User Image">
                    <span class="d-none d-md-inline">{{ Auth::user()->name }}</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                    <!-- User image -->
                    <li class="user-header bg-main">
                        <img src="{{ env('PUBLIC_PATH')}}images/adminlogo.svg"
                             class="img-circle elevation-2"
                             alt="User Image">
                        <p>
                            {{ Auth::user()->name }}
                        </p>
                    </li>
                    <!-- Menu Footer-->
                    <li class="user-footer">
                        <a href="#" class="btn btn-default btn-flat float-right"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            Sign out
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>

    <!-- Left side column. contains the logo and sidebar -->
@include('admin.layouts.sidebar')

<!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <section class="content">
            @yield('content')
        </section>
    </div>

    <!-- Main Footer -->
    <footer class="main-footer">
        <strong>
           Copyright &copy; <?= date('Y'); ?> <a href="{{ env('APP_URL') }}/admin"><?php echo env('APP_NAME'); ?></a>.
        </strong>
        All rights reserved.
    </footer>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.min.js"></script>

<!-- AdminLTE App -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.1.0/js/adminlte.min.js" ></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js" ></script>

<script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/js/bootstrap4-toggle.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-switch/1.3/bootstrapSwitch.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/2.4.3/js/adminlte.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/iCheck/1.0.2/icheck.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.min.js"></script>
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/v/dt/dt-1.10.12/datatables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.colVis.min.js"></script>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-switch/3.3.4/js/bootstrap-switch.min.js"></script> -->
<script>
   
    jQuery(document).ready(function() {
        var dataTable = $('#datatbl1').DataTable({

            paging: true,
            columnDefs: [{
                sortable: true,
                "class": "index",
                targets: 1
            }],

            fixedColumns: true,
            "aoColumnDefs": [{
                "bSortable": false
            }],
            createdRow: function( row, data, dataIndex ) {
                // Set the data-status attribute, and add a class
                $( row ).find('td')
                    .addClass('align-middle');
            }
        });
        dataTable.on('order.dt search.dt', function() {
            dataTable.column(0, {
                search: 'applied',
                order: 'applied'
            }).nodes().each(function(cell, i) {

            });
        }).draw();
       
    });

    function plugspaceRank(user_id)
    {   
        var plugspace_rank = $('#plugspace_rank_'+user_id).val();
        if(plugspace_rank >= 1  && plugspace_rank <= 10 ){
            $.ajax({
                url : 'plugspaceRank',
                type : "post",
                data : {
                    'user_id' : user_id,
                    'plugspace_rank' : plugspace_rank,
                    '_token': $('input[name=_token]').val(),
                },
                success : function(resp)
                {
                    document.getElementById('result').className = 'alert alert-success';
                    $('#result').html(resp);
                }
            });
        }else{
            alert('Please enter plugspace rank between 1 to 10.');
        }
    }

    function isNumber(evt) {
        var iKeyCode = (evt.which) ? evt.which : evt.keyCode
        if (iKeyCode != 46 && iKeyCode > 31 && (iKeyCode < 48 || iKeyCode > 57))
            return false;
        return true;
    } 
    
    function editUser(id)
    {
        $.ajax({
            url : 'editUser',
            type : "post",
            data : {
                'id' : id,
                '_token': $('input[name=_token]').val(),
            },
            success : function(resp)
            {
               $('#id').val(resp.data.id);
               $('#name').val(resp.data.name);
               $('#rank').val(resp.data.rank);
               $('#rank select:eq('+resp.data.rank+')').prop('selected', true);
               $('#gender').val(resp.data.gender);
               $('#gender select:eq('+resp.data.gender+')').prop('selected', true);

               $('#editCategory').modal('show');
            }
        });
    }

    function editText(id)
    {
        $.ajax({
            url : 'editText',
            type : "post",
            data : {
                'id' : id,
                '_token': $('input[name=_token]').val(),
            },
            success : function(resp)
            {
               $('#id').val(resp.data.id);
               $('#name').val(resp.data.name);
               $('#text').val(resp.data.text);
               $('#rank').val(resp.data.rank);
               $('#rank select:eq('+resp.data.rank+')').prop('selected', true);
               $('#editCategory').modal('show');
            }
        });
    }

    function editMessage(id)
    {
        $.ajax({
            url : 'editMessage',
            type : "post",
            data : {
                'id' : id,
                '_token': $('input[name=_token]').val(),
            },
            success : function(resp)
            {
               $('#id').val(resp.data.id);
               $('#text').val(resp.data.message);
               $('#editCategory').modal('show');
            }
        });
    }

    $(document).ready(function() {
    $('#plugspace-rank').DataTable( {
        "ordering": false,
        initComplete: function () {
            this.api().columns([1,3]).every( function () {
                var column = this;
                var select = $('<select><option value="">All</option></select>')
                    .appendTo( $(column.footer()).empty() )
                    .on( 'change', function () {
                        var val = $.fn.dataTable.util.escapeRegex(
                            $(this).val()
                        );
                               
                        column
                            .search( val ? '^'+val+'$' : '', true, false )
                            .draw();
                    } );
                column.data().unique().sort().each( function ( d, j ) {
                    select.append( '<option value="'+d+'">'+d+'</option>' )
                } );
            } );
        }
    } );
 });
   

    $(document).ready(function() {
    $('#plugspace-rank-text').DataTable( {
        "ordering": false,
        initComplete: function () {
            this.api().columns([1]).every( function () {
                var column = this;
                var select = $('<select><option value="">All</option></select>')
                    .appendTo( $(column.footer()).empty() )
                    .on( 'change', function () {
                        var val = $.fn.dataTable.util.escapeRegex(
                            $(this).val()
                        );
                               
                        column
                            .search( val ? '^'+val+'$' : '', true, false )
                            .draw();
                    } );
                column.data().unique().sort().each( function ( d, j ) {
                    select.append( '<option value="'+d+'">'+d+'</option>' )
                } );
            } );
        }
    } );
 });
 
  var counter = 1;
  var totalInputs = 0;
  var totalInputsCount = 1;

  $(function() {
        //add new size div
        $(document).on("click",".plus2",function(){
            //$('.plus').click(function() {
          totalInputs++;
          totalInputsCount++;
          var counter = 1;
          var class1 = "calling_" + totalInputs;
          var img = "<?=env("PUBLIC_PATH").("/admin_images/images.png")?>";
          if(totalInputs < 10){
               var html = '<div class="'+class1+'"><br><label>Name: <b style="margin-left: 325px;">Count : '+totalInputsCount+'</b></label><input type="text" name="name[]" required class="form-control"></div>';
               $(".addteamDiv").append(html);
          }
         

        });

  $(document).on("click",".min2",function(){
          $(".calling_" + totalInputs).remove();
          totalInputs--;
          if(counter>1)
          {
            counter--;
          }
      });
  });

function deleteUsers(user_id)
{
    var choose = confirm('Are you sure want to delete this user?');

    if(choose==true)
    {
        $.ajax({
            url : 'deleteUsers',
            type : "post",
            data : {
                'user_id' : user_id,
                '_token': $('input[name=_token]').val(),
            },
            success : function(resp)
            {
                document.getElementById('result').className = 'alert alert-success';
                $('#result').html(resp);
                location.reload();
            }
        });
    }
}


function changeStatus(id,status,status1)
{   
    if(status == '1'){
        var choose = confirm('Are you sure you want deactive this user?');
    }else if(status == '0'){
        var choose = confirm('Are you sure you want active this user?');
    }
        
    if(choose==true)
    {
        $.ajax({
          url: 'userStatus',
          type : "post",
          data : {
            '_token': $('input[name=_token]').val(),
            user_id : id,
            status : status
          },
          success : function(resp)
          {
            document.getElementById('result').className = 'alert alert-success';
                $('#result').html(resp);
          }
        });
    }else{
        if(status1 == 'unchecked')
        { 
          $('#slider_'+id).prop('checked',true);
        }else if(status1 == 'checked'){
          $('#slider_'+id).prop('checked',false); 
        }else{
          $('#slider_'+id).prop('checked',false); 
        }    
    }
 }

 function removeMedia(id,type)
 {
    var choose = confirm('Are you sure want to delete this user media?');

    if(choose==true)
    {
        $.ajax({
            url : "{{URL::to('admin/removeMedia')}}",
            type : "post",
            data : {
                'id' : id,
                'type' : type,
                '_token': $('input[name=_token]').val(),
            },
            success : function(resp)
            {
                document.getElementById('result').className = 'alert alert-success';
                $('#result').html(resp);
                location.reload();
            }
        });
    }
 }
 
 function removeUsers(id)
{
    var choose = confirm('Are you sure want to delete this user?');

    if(choose==true)
    {
        $.ajax({
            url : 'removeUsers',
            type : "post",
            data : {
                'id' : id,
                '_token': $('input[name=_token]').val(),
            },
            success : function(resp)
            {
                document.getElementById('result').className = 'alert alert-success';
                $('#result').html(resp);
                location.reload();
            }
        });
    }
}
 function deleteText(id)
{
    var choose = confirm('Are you sure want to delete this text?');

    if(choose==true)
    {
        $.ajax({
            url : 'deleteText',
            type : "post",
            data : {
                'id' : id,
                '_token': $('input[name=_token]').val(),
            },
            success : function(resp)
            {
                document.getElementById('result').className = 'alert alert-success';
                $('#result').html(resp);
                location.reload();
            }
        });
    }
}

function deleteMessage(id)
{
    var choose = confirm('Are you sure want to delete this message?');

    if(choose==true)
    {
        $.ajax({
            url : 'deleteMessage',
            type : "post",
            data : {
                'id' : id,
                '_token': $('input[name=_token]').val(),
            },
            success : function(resp)
            {
                document.getElementById('result').className = 'alert alert-success';
                $('#result').html(resp);
                location.reload();
            }
        });
    }
}
</script>

@stack('third_party_scripts')

@stack('page_scripts')
</body>
</html>
