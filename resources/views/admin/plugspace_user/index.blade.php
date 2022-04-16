@extends('admin.layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>PlugSpace Rank</h1>
                </div>
                <div class="col-md-6">
                    <button class="btn btn-primary float-right" data-toggle="modal" data-target="#categoryModal">Add PlugSpace User</button>
                </div>
            </div>
        </div>
    </section>
    <div class="content px-3">

        @include('flash::message')
        <div id="result"></div>  
        <div class="clearfix"></div>

        <div class="card">
            <div class="card-body p-3">
                @include('admin.plugspace_user.table')
                <div class="card-footer clearfix float-right">
                    <div class="float-right">
                    </div>
                </div>
            </div>
        </div>
    </div>
   </div>
    <div class="modal fade" id="categoryModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Create User</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
            <form method="post" action="{{ url('admin/addUser') }}">
                @csrf
                <div class="modal-body">
                    <label>Select Gender:</label>
                    <select class="form-control" name="gender" >
                      <option value="male">Male</option>
                      <option value="female">Female</option>
                      <option value="other">Other</option>
                    </select>
                </div>
                <div class="modal-body">
                    <label>Select Rank:</label>
                    <select class="form-control" name="rank" >
                        <?php for ($i=1; $i < 11; $i++) {  ?>
                            <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                        <?php } ?>
                       
                    </select>
                    
                </div>
                 <div class="modal-body row" style="justify-content: right;margin-right: auto;">
                 <button type="button" class="plus2 btn btn-md btn-info" ><i class="ace-icon fa fa-plus"></i></button>&nbsp;&nbsp;&nbsp;
                  <button type="button" class="min2 btn btn-md btn-info"><i class="ace-icon fa fa-minus"></i></button>
                </div>
                <div class="modal-body">
                    <label>Name: <b style="margin-left: 325px;">Count : 1</b></label>
                    <input type="text" name="name[]" required class="form-control">
                      <div class="addteamDiv">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
      </div>
    </div> 
     <div class="modal fade" id="editCategory" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabels" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabels">Edit User</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
            <form method="post" action="{{ url('admin/updateUser') }}">
                @csrf
                <div class="modal-body">
                    <label>Select Gender:</label>
                    <select class="form-control" name="gender" id="gender">
                      <option value="male">Male</option>
                      <option value="female">Female</option>
                      <option value="other">Other</option>
                    </select>
                </div>
                <div class="modal-body">
                    <label>Select Rank:</label>
                    <select class="form-control" name="rank" id="rank">
                        <?php for ($i=1; $i < 11; $i++) {  ?>
                            <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                        <?php } ?>
                    </select>
                </div>
                
                <div class="modal-body">
                    <label>Name:</label>
                    <input type="text" name="name" required class="form-control" id="name">
                  
                    <input type="hidden" name="id" id="id">
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
      </div>
    </div>
@endsection

@section('third_party_scripts')
<script type="text/javascript">

function deleteCategory(cat_id)
{
    var choose = confirm('Are you sure want to delete this category?');

    if(choose==true)
    {
        $.ajax({
            url : 'deleteCategory',
            type : "post",
            data : {
                'cat_id' : cat_id,
                '_token': $('input[name=_token]').val(),
            },
            success : function(resp)
            {
                document.getElementById('result').className = 'alert alert-danger';
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
                document.getElementById('result').className = 'alert alert-danger';
                $('#result').html(resp);
                location.reload();
            }
        });
    }
}


</script>
@endsection

