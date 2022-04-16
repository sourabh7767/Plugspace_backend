@extends('admin.layouts.app')

@section('content')
<section class="content-header">
    <div class="cl12 col-md-6 col-lg-4 contain er-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><b>User Details</b></h1>
            </div>
        </div>
    </div>
</section>
<div class="content">
    <div class="clearfix"></div>
    <p class="flash">
    @include('flash::message')
    <div id="result"></div>
    </p>
    <div class="clearfix"></div>
   <div class="box box-primary">
        <div class="box-body">
             <h5 class="heading"><strong>View User Details</strong></h5>
            <div class="row bg-color">
                <div class="col-12 col-sm-6 col-md-6 col-lg-3 contain ">
                    <p class="">Uniquen ID</p>
                    <p><strong><?php echo $userDtl->user_id; ?> <span><?php if($userDtl->status == '0'){echo "Active"; }else{ echo 'Deactive'; } ?></span></strong></p>
                </div>
                <div class="col-12 col-sm-6 col-md-6 col-lg-3 contain ">
                    <p>Name</p>
                    <p><strong><?php echo $userDtl->name; ?></strong></p>
                </div>
                <div class="col-12 col-sm-6 col-md-6 col-lg-3 contain ">
                    <p>Mobile Number</p>
                    <p><strong><?php if($userDtl->phone != ''){echo $userDtl->ccode."&nbsp;".$userDtl->phone;}else{echo "--";} ?></strong></p>
                </div>
                <div class="col-12 col-sm-6 col-md-6 col-lg-3 contain ">
                    <p>Gender</p>
                    <p><strong><?php echo $userDtl->gender; ?></strong></p>
                </div>
                <div class="col-12 col-sm-6 col-md-6 col-lg-3 contain ">
                    <p>Rank</p>
                    <p><strong><?php echo $userDtl->rank; ?></strong></p>
                </div>
                <div class="col-12 col-sm-6 col-md-6 col-lg-3 contain ">
                    <p>Location</p>
                   <p><strong><?php echo $userDtl->location; ?></strong></p>
                </div>
                <div class="col-12 col-sm-6 col-md-6 col-lg-3 contain ">
                    <p>Height</p>
                   <p><strong><?php echo $userDtl->height; ?></strong></p>
                </div>
                <div class="col-12 col-sm-6 col-md-6 col-lg-3 contain ">
                    <p>Weight</p>
                    <p><strong><?php echo $userDtl->weight; ?></strong></p>
                </div>
                 <div class="col-12 col-sm-6 col-md-6 col-lg-3 contain ">
                    <p>Education Status</p>
                   <p><strong><?php echo $userDtl->education_status; ?></strong></p>
                </div>
                 <div class="col-12 col-sm-6 col-md-6 col-lg-3 contain ">
                    <p>DOB</p>
                    <p><strong><?php echo $userDtl->dob; ?></strong></p>
                </div>
                 <div class="col-12 col-sm-6 col-md-6 col-lg-3 contain ">
                    <p>Children</p>
                    <p><strong><?php echo $userDtl->children; ?></strong></p>
                </div>
                 <div class="col-12 col-sm-6 col-md-6 col-lg-3 contain ">
                    <p>Want Childrens</p>
                    <p><strong><?php echo $userDtl->want_childrens; ?></strong></p>
                </div>
                 <div class="col-12 col-sm-6 col-md-6 col-lg-3 contain ">
                    <p>Marring Race</p>
                    <p><strong><?php echo $userDtl->marring_race; ?></strong></p>
                </div>
                 <div class="col-12 col-sm-6 col-md-6 col-lg-3 contain ">
                    <p>Relationship Status</p>
                    <p><strong><?php echo $userDtl->relationship_status; ?></strong></p>
                </div>

                  <div class="col-12 col-sm-6 col-md-6 col-lg-3 contain ">
                    <p>Ethinicity</p>
                    <p><strong><?php echo $userDtl->ethinicity; ?></strong></p>
                </div>
                <div class="col-12 col-sm-6 col-md-6 col-lg-3 contain ">
                    <p>Company Name</p>
                    <p><strong><?php echo $userDtl->company_name; ?></strong></p>
                </div>
                <div class="col-12 col-sm-6 col-md-6 col-lg-3 contain ">
                    <p>Job Title</p>
                    <p><strong><?php echo $userDtl->job_title; ?></strong></p>
                </div>
                 <div class="col-12 col-sm-6 col-md-6 col-lg-3 contain ">
                    <p>Make Over</p>
                    <p><strong><?php echo $userDtl->make_over; ?></strong></p>
                </div>
                 <div class="col-12 col-sm-6 col-md-6 col-lg-3 contain ">
                    <p>Times Of Engaged</p>
                    <p><strong><?php echo $userDtl->times_of_engaged; ?></strong></p>
                </div>
                 <div class="col-12 col-sm-6 col-md-6 col-lg-3 contain ">
                    <p>My Self Men</p>
                    <p><strong><?php echo $userDtl->my_self_men; ?></strong></p>
                </div>

                <div class="col-12  contain ">
                    <p>Description</p>
                    <p><strong><?php echo $userDtl->about_you; ?></strong></p>
                </div>

                <div class="col-12 col-sm-6 col-md-6 col-lg-3 contain ">
                    <p>View</p>
                    <p><strong><?php echo $userDtl->view; ?></strong></p>
                </div>
                <div class="col-12 col-sm-6 col-md-6 col-lg-3 contain ">
                    <p>Likes</p>
                    <p><strong><?php echo $userDtl->likes; ?></strong></p>
                </div>
            </div>
        </div>
     </div>
  </div>

   <div class="box box-primary">
        <div class="box-body">
            <h5 class="heading"><strong>View User Profile & Feed</strong></h5>
             <div class="row bg-color">

                 <?php if(count($userDtl->media_detail)>0){ ?>
                <?php foreach ($userDtl->media_detail as $key => $value) { ?>

                        <?php if($value->media_type == 'image'){ ?>
                            <?php if($value->type == 'feed'){ ?>
                                <div class="col-sm-6 col-md-6 col-fix col-lg-4 ">
                                    <div class="box-user">
                                        <div class="col-10  User-contant m-auto text-center">
                                            <div class="img-box">
                                                <a href="#my_modal" data-toggle="modal" data-media="{{ $value->feed_image }}" data-media-type="{{$value->media_type}}">
                                                <img src="{{ $value->feed_image }}"  alt="">
                                                </a>
                                            </div>
                                         <button type="button" class="btn  delete-btn" onclick="removeMedia('<?php echo $value->feed_id; ?>','feed')"> <i class="fas fa-trash"></i> Delete</button>
                                         </div>
                                    </div>
                                </div>
                            <?php }else{ ?>
                                <div class="col-sm-6 col-md-6 col-fix col-lg-4 ">
                                    <div class="box-user">
                                        <div class="col-10  User-contant m-auto text-center">
                                            <div class="img-box">
                                                <a href="#my_modal" data-toggle="modal" data-media="{{ $value->profile }}" data-media-type="{{$value->media_type}}">
                                                    <img src="{{ $value->profile }}"  alt="">
                                                </a>
                                            </div>
                                         <button type="button" class="btn  delete-btn" onclick="removeMedia('<?php echo $value->media_id; ?>','profile')"> <i class="fas fa-trash"></i> Delete</button>
                                         </div>
                                    </div>
                                </div>
                            <?php } ?>
                        <?php } else { ?>
                            <div class="col-sm-6 col-md-6 col-fix col-lg-4 ">
                                <div class="box-user">
                                    <div class="col-10  User-contant m-auto text-center">
                                        <div class="img-box">
                                            <a href="#my_modal" data-toggle="modal" data-media="{{ $value->feed_image }}" data-media-type="{{$value->media_type}}">
                                            <video width="220" height="140" controls>
                                                <source src="{{ $value->feed_image }}" >
                                            </video>
                                            </a>
                                        </div>
                                        <button type="button" class="btn  delete-btn" onclick="removeMedia('<?php echo $value->feed_id; ?>','feed')"> <i class="fas fa-trash"></i> Delete</button>
                                     </div>
                                </div>
                            </div>
                        <?php } ?>
                <?php } ?>
                <?php } else{ ?>
                  <h4>Data not found</h4>
                <?php } ?>
             </div>
        </div>
    </div>

    <div class="box box-primary">
        <div class="box-body">
            <h5 class="heading"><strong>View User Story</strong></h5>
             <div class="row bg-color">
                <?php if(count($userDtl->story_detail)>0){ ?>

                <?php foreach ($userDtl->story_detail as $key => $value) { ?>
                        <?php if($value->media_type == 'image'){ ?>
                                <div class="col-sm-6 col-md-6 col-fix col-lg-4 ">
                                    <div class="box-user">
                                        <div class="col-10  User-contant m-auto text-center">
                                            <div class="img-box">
                                                <a href="#my_modal" data-toggle="modal" data-media="{{ $value->media }}" data-media-type="{{$value->media_type}}">
                                                    <img src="{{ $value->media }}"  alt="">
                                                </a>
                                            </div>
                                         <button type="button" class="btn  delete-btn" onclick="removeMedia('<?php echo $value->story_media_id; ?>','story')"> <i class="fas fa-trash"></i> Delete</button>
                                         </div>
                                    </div>
                                </div>
                        <?php } else { ?>
                            <div class="col-sm-6 col-md-6 col-fix col-lg-4 ">
                                <div class="box-user">
                                    <div class="col-10  User-contant m-auto text-center">
                                        <div class="img-box">
                                            <a href="#my_modal" data-toggle="modal" data-media="{{ $value->media }}" data-media-type="{{$value->media_type}}">
                                            <video width="220" height="140" controls>
                                                <source src="{{ $value->media }}" >
                                            </video>
                                            </a>
                                        </div>
                                        <button type="button" class="btn  delete-btn" onclick="removeMedia('<?php echo $value->story_media_id; ?>','story')"> <i class="fas fa-trash"></i> Delete</button>
                                     </div>
                                </div>
                            </div>
                        <?php } ?>
                <?php } ?>



            <?php } else{ ?>
              <h4>Data not found</h4>
            <?php } ?>
             </div>
        </div>
    </div>
<div class="modal" id="my_modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            </div>
            <div class="modal-body">
                <div id="media_show" class="img-box" style="text-align:center"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
@push('page_scripts')
    <script>
        $('#my_modal').on('show.bs.modal', function(e) {
            let media = $(e.relatedTarget).data('media');
            let mediaType = $(e.relatedTarget).data('media-type');
            if(mediaType == 'image'){
                $(e.currentTarget).find('#media_show').html('<img src="'+media+'"  alt="" style="width:600px">')
            }else{
                $(e.currentTarget).find('#media_show').html(`<video width="600" height="380" controls>
                    <source src="${media}" >
                    </video>`)
            }

        });
    </script>
@endpush
