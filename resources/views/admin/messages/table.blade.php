<div class="table-responsive">
    <table class="table table-striped" id="plugspace-rank-text">
         
        <thead>
            <tr>
                <th>Sr.</th>
                <th>Message</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php $n=1; ?>
            @foreach($messages ?? '' as $userDtls)
                <tr>
                    <td>{{ $n++ }}</td>
                    <td>{{ $userDtls->message }}</td>
                    <td width="10%">
                        <div class="btn-group" style="margin-left: 20px;">
                            <button onclick="editMessage('<?php echo $userDtls->id; ?>')" class='btn btn-default btn-xs'><i class="far fa-edit"></i></button>
                      
                        </div>
                        <div class="btn-group" style="margin-left: 10px;">
                            <button onclick="deleteMessage('<?php echo $userDtls->id; ?>')" class='btn btn-default btn-xs'><i class="fas fa-trash"></i></button>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
