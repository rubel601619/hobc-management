<tr class="">
    <td class="title column-title has-row-actions column-primary page-title" data-colname="Title">
        <strong>
            <a class="row-title" href="<?php echo esc_url( $edit_url ); ?>"><?php echo esc_html( $player->display_name ); ?></a>
        </strong>
        
        <div class="row-actions custom-row-action">
            <span class="edit">
                <a href="<?php echo esc_url( $edit_url ); ?>">Edit</a> | 
            </span>
            <span class="trash">
                <a href="<?php echo esc_url( $delete_url ); ?>" onclick="return confirm('Are you sure you want to delete this player?')" class="submitdelete">Trash</a>
            </span>
        </div>
    </td>
    <td class="author column-author">
        <a href="<?php echo esc_url( $edit_url ); ?>"><?php echo esc_html($player->user_email); ?></a>
    </td>
</tr>