<div class="table-responsive">
    <table class="table table-striped" id="plugspace-rank">
         <tfoot>
            <tr>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
            </tr>
           </tfoot>
        <thead>
            <tr>
                <th>Sr.</th>
                <th>PlugSpace Rank</th>
                <th>Name</th>
                <th>Gender</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php $n=1; ?>
            @foreach($userDtl ?? '' as $userDtls)
                <tr>
                    <td>{{ $n++ }}</td>
                    <td>{{ $userDtls->rank }}</td>
                    <td>{{ $userDtls->name }}</td>
                    <td><?php echo ucfirst($userDtls->gender); ?></td>
                    <td>
                        <div class="btn-group" style="margin-left: 20px;">
                            <button onclick="editUser('<?php echo $userDtls->id; ?>')" class='btn btn-default btn-xs'><i class="far fa-edit"></i></button>
                      
                        </div>
                        <div class="btn-group" style="margin-left: 10px;">
                            <button onclick="removeUsers('<?php echo $userDtls->id; ?>')" class='btn btn-default btn-xs'><i class="fas fa-trash"></i></button>
                      
                        </div>
                    </td>
                    
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
