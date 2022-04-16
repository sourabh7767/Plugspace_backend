<div class="table-responsive">
    <table class="table table-striped" id="datatbl1">
        <thead>
            <tr style="font-size: 15px;">
                <th style="text-align: center;" class="align-middle">UNIQUE ID</th>

                <th style="text-align: center;" class="align-middle">USERNAME</th>
                <th style="text-align: center;" class="align-middle">PHONE</th>
                <th style="text-align: center;" class="align-middle">GENDER</th>
                <th style="text-align: center;" class="align-middle">MY RANK</th>
                <th style="text-align: center;" class="align-middle">PlugSpace RANK</th>
                <th style="text-align: center;" class="align-middle">STATUS</th>
                <th style="text-align: center;" class="align-middle">CREATED ON</th>
                <th style="text-align: center;" class="align-middle">VIEW</th>
                <th style="text-align: center;" class="align-middle">DELETE</th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 0 ?>
            @foreach($users as $user)
            <tr>
                <?php $i = $i + 1 ?>
                <td style="text-align: center;" class="align-middle"><?php echo $i ?></td>
                <td style="text-align: center;" class="align-middle">{!! $user->name !!}</td>
                <td style="text-align: center;" class="align-middle">{!! $user->ccode !!} {!! $user->phone !!}</td>
                <td style="text-align: center;" class="align-middle">{!! $user->gender !!}</td>
                <td style="text-align: center;" class="align-middle">{!! $user->rank !!}</td>
                <td style="text-align: center;" class="align-middle"><input type="text" name="rank" style="width: 72px;" value="<?php echo $user->plugspace_rank; ?>" 
                    id="plugspace_rank_<?php echo $user->user_id; ?>" onkeypress="javascript:return isNumber(event); return false;" class="form-control">&nbsp;<button class="btn btn-primary" onclick="plugspaceRank('<?php echo $user->user_id; ?>')">Save</button> </td>
                <td>
                  @if($user->status == '0') 
                  <label>
                      <a class="switch-cstm"  onchange="changeStatus('{{ $user->user_id }}','1','unchecked')">
                          <input type="checkbox" id="slider_<?php echo $user->user_id; ?>" {{ $user->status == '0' ? 'checked' : '' }}>
                          <span class="slider-cstm round" title="Header" data-toggle="popover" data-placement="top" data-content="Content" ></span>
                      </a>
                  </label>   
                 
                 @else
                  <label>
                      <a class="switch-cstm"  onchange="changeStatus('{{ $user->user_id }}','0','checked')">
                          <input type="checkbox" id="slider_<?php echo $user->user_id; ?>" {{$user->status == '1'  ? '' : 'checked' }} >
                          <span class="slider-cstm round" title="Header" data-toggle="popover" data-placement="top" data-content="Content" ></span>
                      </a>
                  </label>    
                 @endif 
                </td>
                <td style="text-align: center;" class="align-middle"><?php echo date('Y-m-d',strtotime($user->created_at)); ?></td>

                <td><a href="{{ url('admin/userDetails') }}/<?php echo $user->user_id; ?>"><i class="fas fa-eye"></i></a></td>
                <td><a type="button"  onclick="deleteUsers('<?php echo $user->user_id; ?>')"><i class="fas fa-trash" style="color: #3c8dbc;"></i></a></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
