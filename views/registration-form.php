<form method="post" enctype="multipart/form-data">
    
    <div class="item-row">
        <p class="regponsive-item">
            <label for="hobc_name">Name</label><br>
            <input type="text" name="hobc_name" id="hobc_name" required>
        </p>
        <p class="regponsive-item">
            <label for="hobc_contact">Contact</label><br>
            <input type="text" name="hobc_contact" id="hobc_contact" required>
        </p>
        <p class="regponsive-item">
            <label for="hobc_category">Category</label><br>
            <input type="text" name="hobc_category" id="hobc_category" required>
        </p>
        <p class="regponsive-item">
            <label for="hobc_club_team">Club/Team</label><br>
            <input type="text" name="hobc_club_team" id="hobc_club_team" required>
        </p>
        <p>
            <label for="hobc_email">Email</label><br>
            <input type="email" name="hobc_email" id="hobc_email" required>
        </p>
        <p>
            <label for="hobc_password">Password</label><br>
            <input type="password" name="hobc_password" id="hobc_password" required>
        </p>
        <p>
            <label for="hobc_profile_image">Profile Image</label><br>
            <input type="file" name="hobc_profile_image" id="hobc_profile_image" accept="image/*">
        </p>
    </div>
    
    <p>
        <label class="hobc-d-flex">
            <input type="checkbox" name="hobc_terms" required>
            <span>I agree to the terms & conditions</span>
        </label>
    </p>
    <p>
        <input type="submit" name="hobc_register_user" value="Register">
    </p>
</form>

<style>
    .hobc-d-flex{
        display: flex;
        align-items: center;
        gap: 5px;
        user-select: none;
    }
</style>