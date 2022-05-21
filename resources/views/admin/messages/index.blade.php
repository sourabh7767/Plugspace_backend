@extends('admin.layouts.app')

@section('content')

    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Sample Messages</h1>
                </div>
                <div class="col-md-6" >
                    <button class="btn btn-primary float-right" data-toggle="modal" data-target="#categoryModal">Add Sample Message</button>
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
                @include('admin.messages.table')
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
            <h5 class="modal-title" id="exampleModalLabel">Sample Message</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
            <form method="post" action="{{ url('admin/addMessage') }}">
                @csrf
                <div class="modal-body">
                    <label>Message:</label>
                    <textarea name="text" class="form-control" required></textarea>
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
            <h5 class="modal-title" id="exampleModalLabels">Edit Message</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
            <form method="post" action="{{ url('admin/updateMessage') }}">
                @csrf
                <div class="modal-body">
                    <label>Message:</label>
                    <textarea name="text" class="form-control" id="text" required></textarea>
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

function deleteMessage(cat_id)
{
    var choose = confirm('Are you sure want to delete this message?');

    if(choose==true)
    {
        $.ajax({
            url : 'deleteMessage',
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


</script>
@endsection

