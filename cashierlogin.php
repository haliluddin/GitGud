<?php
    include_once 'links.php'; 
?>
<style>
    main {
        display: flex;
        height: calc(100vh - 65.61px); 
        overflow: hidden;
        background-color: white;
    }
    .fixed-image {
        width: 50%;
    }
    .srform {
        width: 50%;
        border: none;
        padding: 100px;
    }
    .form-floating input, .form-floating textarea, .form-floating label::after, .logo, .add-schedule, .schedule-list{
        background-color: #F8F8F8 !important;
    }
</style>
<div class="bottom d-flex justify-content-between align-items-center">
    <a href="centralized.php"><img src="assets/images/logo.png" alt="GitGud"></a>
</div> 
<main>
    <img src="assets/images/cash.jpg" class="fixed-image">

    <form action="centralizedcash.php" class="srform">
        <h4 class="fw-bold">Login</h4>
        <div class="input-group">
            <label for="email">Email</label>
            <input type="text" name="email" id="email" placeholder="Enter your email" required/>
        </div>
        <div class="input-group mb-2">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" placeholder="Enter your password" required/>
        </div>
        <br>
        <div class="btns-group d-block text-center">
            <input type="submit" value="Sign In" class="button">
        </div>
    </form>
    
</main>



