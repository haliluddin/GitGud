<?php
    include_once 'links.php'; 
    include_once 'secondheader.php'; 
?>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="assets/css/styles.css?v=<?php echo time(); ?>">
<style>
    .main-pp {
        display: flex;
        height: calc(100vh - 77.61px); 
        overflow: hidden;
        background-color: white;
    }
    .penappleft {
        background-color: #f4f4f4;
        text-align: center;
    }
    .penappleft, .penappright{
        display: flex;
        justify-content: center;
        width: 50%;
        align-items: center;
    }
    .penappleft img{
        width: 200px;
        height: 200px;
        margin-bottom: 20px;
    }
</style>
<main class="main-pp">
    <div class="penappleft">
        <div>
            <img src="assets/images/approve.png" alt="">
            <h1 class="fw-bold">Document are being verified</h1>
            <span>It could take up to 2 working days to verify your document.</span>
        </div>
    </div>
    <div class="penappright">
        <div>
            <div class="step completed">
                <div class="circle">✓</div>
                <div class="content">
                    <div class="title">The registration has been generated successfully!</div>
                    <div class="description">You can download a copy from the email we have sent. Keep it safe!</div>
                    <div class="status">Completed</div>
                </div>
            </div>

            <div class="step review">
                <div class="circle">✓</div>
                <div class="content">
                    <div class="title">Document approval</div>
                    <div class="description">You have uploaded everything we need.</div>
                    <div class="status">Under Review</div>
                </div>
            </div>

            <div class="step incomplete">
                <div class="circle"></div>
                <div class="content">
                    <div class="title">Add your stall</div>
                    <div class="description">Send invitation link to your stall owners and manage their rental.</div>
                </div>
            </div>

            <div class="step incomplete">
                <div class="circle"></div>
                <div class="content">
                    <div class="title">Activate centralized cash payment</div>
                    <div class="description">You can activate or not this feature.</div>
                </div>
            </div>
        </div>
    </div>
</main>

