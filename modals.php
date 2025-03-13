<style>
    .btn{
        width: 150px;
    }
</style>

<!-- Delete Stall -->
<div class="modal fade" id="deletestall" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
<div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-body">
        <div class="d-flex justify-content-end">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="text-center">
            <h4 class="fw-bold mb-4"><i class="fa-solid fa-circle-exclamation"></i> Delete Stall</h4>
            <span>You are about to delete this stall.<br>Are you sure?</span>
            <div class="mt-5 mb-3">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary">Delete</button>
            </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Delete Product -->
<div class="modal fade" id="deleteproduct" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
<div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-body">
        <div class="d-flex justify-content-end">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="text-center">
            <h4 class="fw-bold mb-4"><i class="fa-solid fa-circle-exclamation"></i> Delete Product</h4>
            <span>You are about to delete this product.<br>Are you sure?</span>
            <div class="mt-5 mb-3">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary">Delete</button>
            </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Delete Stall -->
<div class="modal fade" id="deletestock" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
<div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-body">
        <div class="d-flex justify-content-end">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="text-center">
            <h4 class="fw-bold mb-4"><i class="fa-solid fa-circle-exclamation"></i> Delete Stock</h4>
            <span>You are about to delete this stock.<br>Are you sure?</span>
            <div class="mt-5 mb-3">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary">Delete</button>
            </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Add Category
<div class="modal fade" id="addcategory" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="d-flex justify-content-end">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="text-center">
                    <h4 class="fw-bold mb-4">Add Category</h4>
                    <div class="form-floating m-0">
                        <input type="text" class="form-control" id="category" placeholder="Category">
                        <label for="category">Category</label>
                    </div>
                    <div class="mt-5 mb-3">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="addCategoryBtn">Add</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('#addCategoryBtn').click(function() {
            var category = $('#category').val();
            if (category) {
                $.ajax({
                    type: 'POST',
                    url: 'add_category.php',
                    data: { category: category },
                    success: function(response) {
                        alert(response);
                        $('#addcategory').modal('hide');
                        $('#category').val('');
                    },
                    error: function() {
                        alert('Error adding category.');
                    }
                });
            } else {
                alert('Please enter a category name.');
            }
        });
    });
</script>  -->

<!-- Edit Category -->
<div class="modal fade" id="editcategory" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
<div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-body">
        <div class="d-flex justify-content-end">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="text-center">
            <h4 class="fw-bold mb-4">Edit Category</h4>
            <div class="form-floating m-0">
                <input type="text" class="form-control" id="category" placeholder="Category" value="Category 1">
                <label for="category">Category</label>
            </div>
            <div class="mt-5 mb-3">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary">Save</button>
            </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Add Rent Payment -->
<div class="modal fade" id="addpayment" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="d-flex justify-content-end">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="text-center">
                    <h4 class="fw-bold mb-4">Add Payment</h4>
                    <div class="input-group m-0 mb-3">
                        <label for="amountpaid">Amount Paid</label>
                        <input type="number" name="amountpaid" id="amountpaid" placeholder="Enter amount paid"/>            
                    </div>
                    <div class="input-group m-0 mb-3">
                        <label for="datepaid">Date Paid</label>
                        <input type="date" name="datepaid" id="datepaid" placeholder="Enter date paid"/>            
                    </div>
                    <div class="d-flex gap-2">
                        <div class="input-group m-0 mb-3">
                            <label for="startDate">Start Date</label>
                            <input type="date" name="startDate" id="startDate"/>
                        </div>
                        <div class="input-group m-0 mb-3">
                            <label for="endDate">End Date</label>
                            <input type="date" name="endDate" id="endDate"/>
                        </div>
                    </div>
                    <div class="input-group m-0 mb-4">
                        <label for="paymentmethod">Payment Method</label>
                        <select name="paymentmethod" id="paymentmethod" style="padding: 10.5px 0.75rem">
                            <option value="" disabled selected>Select</option>
                            <option value="cash">Cash</option>
                            <option value="gcash">GCash</option>
                            <option value="paymaya">PayMaya</option>
                            <option value="others">Others</option>
                        </select>
                    </div>
                    <button type="button" class="btn btn-primary">Add</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Rent Payment -->
<div class="modal fade" id="editpayment" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="d-flex justify-content-end">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="text-center">
                    <h4 class="fw-bold mb-4">Edit Payment</h4>
                    <div class="input-group m-0 mb-3">
                        <label for="amountpaid">Amount Paid</label>
                        <input type="number" name="amountpaid" id="amountpaid" value="1000" placeholder="Enter amount paid" />
                    </div>
                    <div class="input-group m-0 mb-3">
                        <label for="datepaid">Date Paid</label>
                        <input type="date" name="datepaid" id="datepaid" value="2024-11-01" placeholder="Enter date paid" />
                    </div>
                    <div class="d-flex gap-2">
                        <div class="input-group m-0 mb-3">
                            <label for="startDate">Start Date</label>
                            <input type="date" name="startDate" id="startDate" value="2024-10-01" />
                        </div>
                        <div class="input-group m-0 mb-3">
                            <label for="endDate">End Date</label>
                            <input type="date" name="endDate" id="endDate" value="2024-12-31" />
                        </div>
                    </div>
                    <div class="input-group m-0 mb-4">
                        <label for="paymentmethod">Payment Method</label>
                        <select name="paymentmethod" id="paymentmethod" style="padding: 10.5px 0.75rem">
                            <option value="" disabled>Select</option>
                            <option value="cash" selected>Cash</option>
                            <option value="gcash">GCash</option>
                            <option value="paymaya">PayMaya</option>
                            <option value="others">Others</option>
                        </select>
                    </div>
                    <button type="button" class="btn btn-primary">Save</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add User -->
<div class="modal fade" id="adduser" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content p-4">
            <div class="modal-header p-0 border-0 m-0">
                <h5 class="m-0">Add User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 m-0">
                <form action="#" class="form w-100 border-0 p-0" method="POST">
                    <div class="progressbar">
                        <div class="progress" id="progress"></div>
                        <div class="progress-step progress-step-active" data-title="Name"></div>
                        <div class="progress-step" data-title="Contact"></div>
                        <div class="progress-step" data-title="Other"></div>
                        <div class="progress-step" data-title="Password"></div>
                    </div>

                    <div class="form-step form-step-active">
                        <div class="input-group">
                            <label for="firstname">First Name</label>
                            <input type="text" name="firstname" id="firstname" placeholder="Enter your first name" />
                        </div>
                        <div class="input-group">
                            <label for="lastname">Last Name</label>
                            <input type="text" name="lastname" id="lastname" placeholder="Enter your last name" />
                        </div>
                        <div class="btns-group d-block text-center">
                            <input type="button" value="Next" class="button btn-next" />
                        </div>
                    </div>

                    <div class="form-step">
                        <div class="form-group">
                            <label for="phone" class="mb-2">Phone Number</label>
                            <div class="input-group mt-0">
                                <span class="input-group-text">+63</span>
                                <input type="tel" name="phone" id="phone" class="form-control phone-input" placeholder="Enter your phone number" />
                            </div>
                        </div>
                        <div class="input-group">
                            <label for="email">Email</label>
                            <input type="email" name="email" id="email" placeholder="Enter your email" />
                        </div>
                        <div class="btns-group">
                            <a href="#" class="button btn-prev">Previous</a>
                            <a href="#" class="button btn-next">Next</a>
                        </div>
                    </div>

                    <div class="form-step">
                        <div class="input-group">
                            <label for="dob">Date of Birth</label>
                            <input type="date" name="dob" id="dob" />
                        </div>
                        <div class="input-group">
                            <label for="sex">Sex</label>
                            <select name="sex" id="sex" style="padding: 12px 0.75rem">
                                <option value="" disabled selected>Select your sex</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                        </div>
                        <div class="btns-group">
                            <a href="#" class="button btn-prev">Previous</a>
                            <a href="#" class="button btn-next">Next</a>
                        </div>
                    </div>

                    <div class="form-step">
                        <div class="input-group">
                            <label for="password">Password</label>
                            <input type="password" name="password" id="password" placeholder="Enter your password" />
                        </div>
                        <div class="input-group">
                            <label for="confirm_password">Confirm Password</label>
                            <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm your password" />
                        </div>
                        <div class="btns-group">
                            <a href="#" class="button btn-prev">Previous</a>
                            <input type="submit" value="Add User" class="button" />
                        </div>
                    </div>
                </form>
                <script src="assets/js/adduser.js?v=<?php echo time(); ?>"></script>
            </div>
        </div>
    </div>
</div>

<!-- Edit User -->
<div class="modal fade" id="edituser" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content p-4">
            <div class="modal-header p-0 border-0 m-0">
                <h5 class="m-0">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 m-0">
                <form action="#" class="form w-100 border-0 p-0" method="POST">
                    <div class="progressbar">
                        <div class="progress" id="progress"></div>
                        <div class="progress-step progress-step-active" data-title="Name"></div>
                        <div class="progress-step" data-title="Contact"></div>
                        <div class="progress-step" data-title="Other"></div>
                        <div class="progress-step" data-title="Password"></div>
                    </div>

                    <div class="form-step form-step-active">
                        <div class="input-group">
                            <label for="firstname">First Name</label>
                            <input type="text" name="firstname" id="firstname" placeholder="Enter your first name" value="Naila"/>
                        </div>
                        <div class="input-group">
                            <label for="lastname">Last Name</label>
                            <input type="text" name="lastname" id="lastname" placeholder="Enter your last name" value="Haliluddin"/>
                        </div>
                        <div class="btns-group d-block text-center">
                            <input type="button" value="Next" class="button btn-next" />
                        </div>
                    </div>

                    <div class="form-step">
                        <div class="form-group">
                            <label for="phone" class="mb-2">Phone Number</label>
                            <div class="input-group mt-0">
                                <span class="input-group-text">+63</span>
                                <input type="tel" name="phone" id="phone" class="form-control phone-input" placeholder="Enter your phone number" value="9123456789" />
                            </div>
                        </div>
                        <div class="input-group">
                            <label for="email">Email</label>
                            <input type="email" name="email" id="email" placeholder="Enter your email" value="example@gmail.com"/>
                        </div>
                        <div class="btns-group">
                            <a href="#" class="button btn-prev">Previous</a>
                            <a href="#" class="button btn-next">Next</a>
                        </div>
                    </div>

                    <div class="form-step">
                        <div class="input-group">
                            <label for="dob">Date of Birth</label>
                            <input type="date" name="dob" id="dob" value="2024-12-31"/>
                        </div>
                        <div class="input-group">
                            <label for="sex">Sex</label>
                            <select name="sex" id="sex" style="padding: 12px 0.75rem">
                                <option value="" disabled>Select your sex</option>
                                <option value="male" selected>Male</option>
                                <option value="female">Female</option>
                            </select>
                        </div>
                        <div class="btns-group">
                            <a href="#" class="button btn-prev">Previous</a>
                            <a href="#" class="button btn-next">Next</a>
                        </div>
                    </div>

                    <div class="form-step">
                        <div class="input-group">
                            <label for="password">Password</label>
                            <input type="password" name="password" id="password" placeholder="Enter your password" value="123" />
                        </div>
                        <div class="input-group">
                            <label for="confirm_password">Confirm Password</label>
                            <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm your password" value="123"/>
                        </div>
                        <div class="btns-group">
                            <a href="#" class="button btn-prev">Previous</a>
                            <input type="submit" value="Edit User" class="button" />
                        </div>
                    </div>
                </form>
                <script src="assets/js/edituser.js?v=<?php echo time(); ?>"></script>
            </div>
        </div>
    </div>
</div>

<!-- Delete User -->
<div class="modal fade" id="deleteuser" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <div class="d-flex justify-content-end">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="text-center">
                    <h4 class="fw-bold mb-4"><i class="fa-solid fa-circle-exclamation"></i> Delete User</h4>
                    <span>You are about to delete this user.<br>Are you sure?</span>
                    <div class="mt-5 mb-3">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Suspend User -->
<div class="modal fade" id="deactivateuser" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="modal-title m-0 fw-bold">Select Duration of Deactivation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="flexRadioDefault" id="3days">
                    <label class="form-check-label" for="3days">3 Days</label>
                </div><br>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="flexRadioDefault" id="7days">
                    <label class="form-check-label" for="7days">7 Days</label>
                </div><br>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="flexRadioDefault" id="1month">
                    <label class="form-check-label" for="1month">1 Month</label>
                </div><br>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="flexRadioDefault" id="forever">
                    <label class="form-check-label" for="forever">Forever</label>
                </div><br>
                <div class="text-center mt-4">
                    <button type="button" data-bs-dismiss="modal" class="btn btn-secondary">Close</button>
                    <button type="button" class="btn btn-primary">Deactivate</button> 
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Activate User -->
<div class="modal fade" id="activateuser" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="d-flex justify-content-end">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="text-center">
                    <h4 class="fw-bold mb-4"><i class="fa-solid fa-check"></i> Activate User</h4>
                    <span>You are about to activate this user.<br>Are you sure?</span>
                    <div class="mt-5 mb-3">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary">Activate</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

 <!-- Edit Food Park Modal -->
 <div class="modal fade" id="editfoodpark" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-custom-width">
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-between align-items-center">
                <div class="d-flex gap-4 align-items-center">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Edit Profile</h1>
                </div>
                <button type="submit" class="btn btn-primary send py-2">SAVE</button>
            </div>
            <div class="modal-body modal-scrollable">
                <div class="text-center">
                    <img id="profileImage" src="assets/images/stall1.jpg" width="150px" height="150px" style="border-radius:50%;">
                    <input type="file" id="fileInput" style="display: none;" accept="image/*"><br><br>
                    <button id="uploadButton" class="disatc m-0">Upload Image</button><br><br>
                </div>

                <script>
                    const fileInput = document.getElementById('fileInput');
                    const uploadButton = document.getElementById('uploadButton');
                    const profileImage = document.getElementById('profileImage');

                    uploadButton.addEventListener('click', () => {
                        fileInput.click();
                    });

                    fileInput.addEventListener('change', (event) => {
                        const file = event.target.files[0]; 
                        if (file) {
                            const reader = new FileReader();
                            reader.onload = (e) => {
                                profileImage.src = e.target.result; 
                            };
                            reader.readAsDataURL(file); 
                        }
                    });
                </script>

                <div class="border-top pt-3">
                    <h5 class="fw-bold mb-1">Tell us about your business</h5>
                    <p class="par mb-3">This information will be shown on the web so that customers can search and contact you in case they have any questions.</p>
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="businessname" name="businessname" placeholder="Food Park Name" value="Food Park Name">
                        <label for="businessname">Business Name <span style="color: #CD5C08;">*</span></label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="businessemail" name="businessemail" placeholder="Business Email" value="example@gmail.com">
                        <label for="businessemail">Business Email <span style="color: #CD5C08;">*</span></label>
                    </div>
                    <div class="input-group mb-3 mt-0">
                        <span class="input-group-text">+63</span>
                        <div class="form-floating flex-grow-1">
                            <input type="text" class="form-control" id="businessphonenumber" name="businessphonenumber" placeholder="Business Phone Number" value="9123456789">
                            <label for="businessphonenumber">Business Phone Number <span style="color: #CD5C08;">*</span></label>
                        </div>
                    </div>
                    <div class="operatinghours mb-3">
                        <div class="add-schedule mb-3" style="background-color:#F8F8F8;">
                            <label class="mb-3">What is your business operating hours? <span style="color: #CD5C08;">*</span></label>
                            <div id="timeForm">
                                <div class="oh">
                                    <div class="och mb-3">
                                        <!-- Open Time -->
                                        <label>Open at</label>
                                        <div>
                                            <select name="open_hour" id="open_hour">
                                                <script>
                                                    for (let i = 1; i <= 12; i++) {
                                                        document.write(`<option value="${i}">${String(i).padStart(2, '0')}</option>`);
                                                    }
                                                </script>
                                            </select>
                                            :
                                            <select name="open_minute" id="open_minute">
                                                <script>
                                                    for (let i = 0; i < 60; i++) {
                                                        document.write(`<option value="${i}">${String(i).padStart(2, '0')}</option>`);
                                                    }
                                                </script>
                                            </select>
                                            <select name="open_ampm" id="open_ampm">
                                                <option value="AM">AM</option>
                                                <option value="PM">PM</option>
                                            </select>
                                        </div>
                                    </div>
                                
                                    <div class="och mb-3">
                                        <!-- Close Time -->
                                        <label>Close at</label>
                                        <div>
                                            <select name="close_hour" id="close_hour">
                                                <script>
                                                    for (let i = 1; i <= 12; i++) {
                                                        document.write(`<option value="${i}">${String(i).padStart(2, '0')}</option>`);
                                                    }
                                                </script>
                                            </select>
                                            :
                                            <select name="close_minute" id="close_minute">
                                                <script>
                                                    for (let i = 0; i < 60; i++) {
                                                        document.write(`<option value="${i}">${String(i).padStart(2, '0')}</option>`);
                                                    }
                                                </script>
                                            </select>
                                            <select name="close_ampm" id="close_ampm">
                                                <option value="AM">AM</option>
                                                <option value="PM">PM</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>  
                                <!-- Days of the Week -->
                                <div class="day-checkboxes mb-2">
                                    <label><input type="checkbox" name="days" value="Monday"> Monday</label>
                                    <label><input type="checkbox" name="days" value="Tuesday"> Tuesday</label>
                                    <label><input type="checkbox" name="days" value="Wednesday"> Wednesday</label>
                                    <label><input type="checkbox" name="days" value="Thursday"> Thursday</label>
                                    <label><input type="checkbox" name="days" value="Friday"> Friday</label>
                                    <label><input type="checkbox" name="days" value="Saturday"> Saturday</label>
                                    <label><input type="checkbox" name="days" value="Sunday"> Sunday</label>
                                </div>

                                <button type="button" class="add-hours-btn mt-2" onclick="addOperatingHours()">+ Add</button>
                            </div>
                        </div>
                        
                        <div class="schedule-list" style="background-color:#F8F8F8;">
                            <h6>Operating Hours</h6>
                            <div id="scheduleContainer"></div>
                        </div>
                        <script src="assets/js/editoperatinghours.js?v=<?php echo time(); ?>"></script>
                    </div>
                </div>
                
                <div class="border-top pt-3">
                    <h5 class="fw-bold m-0 mb-1">Where is your business located?</h5>
                    <p class="par mb-3">Customers will use this to find your business for directions and pickup options.</p>
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control c" id="rpc" name="rpc" placeholder="Region, Province, City" value="Mindanao, Zamboanga Del Sur, Zamboanga City" readonly>
                        <label for="rpc">Region, Province, City <span style="color: #CD5C08;">*</span></label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="barangay" name="barangay" placeholder="Barangay" value="Barangay Name">
                        <label for="barangay">Barangay <span style="color: #CD5C08;">*</span></label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="sbh" name="sbh" placeholder="Street Name, Building, House No." value="Unit 3, Building 13, AC">
                        <label for="sbh">Street Name, Building, House No. <span style="color: #CD5C08;">*</span></label>
                    </div>
                </div>

                <div class="border-top pt-3">
                    <h5 class="fw-bold m-0 mb-1">Add your business permit</h5>
                    <p class="par mb-3">We need your legal document to verify and approve your business registration.</p>
                    <label for="fplogo">Upload FULL pages of your Business Permit <span style="color: #CD5C08;">*</span></label>
                    <div class="logocon px-3 py-4 mt-3 text-center border">
                        <img src="assets/images/upload-icon.png" class="w-50 h-50 mb-2" alt=""><br>
                        <span>Maximum of 5MB and can accept only JPG, JPEG, PNG or PDF format</span>
                        <input type="file" id="fplogo" accept="image/jpeg, image/png, image/jpg, application/pdf" name="businesspermit" style="display:none;" />
                    </div>
                    <div id="uploaded-files" class="mt-4">
                        <!-- Uploaded files list will appear here -->
                    </div>
                    <script src="assets/js/editpermit.js?v=<?php echo time(); ?>"></script>
                </div>
                <br><br><br>
            </div>
        </div>
    </div>
</div>

<!-- Delete Park -->
<div class="modal fade" id="deletepark" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <div class="d-flex justify-content-end">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="text-center">
                    <h4 class="fw-bold mb-4"><i class="fa-solid fa-circle-exclamation"></i> Delete Food Park</h4>
                    <span>You are about to delete this food park.<br>Are you sure?</span>
                    <div class="mt-5 mb-3">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Select Food Park -->
 <div class="modal fade" id="selectpark" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 75%;">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Select which park the stall belong</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body modal-scrollable">
                <div class="row row-cols-1 row-cols-md-3 g-3">
                    <div class="col">
                        <a href="#" class="card-link text-decoration-none" data-bs-toggle="modal" data-bs-target="#invitestall">
                            <div class="card" style="position: relative;">
                                <img src="assets/images/foodpark.jpg" class="card-img-top" alt="...">
                                <i class="fa-solid fa-ellipsis-vertical ellipsis-icon"></i>
                                <div class="card-body">
                                    <h5 class="card-title">Food Park Name</h5>
                                    <p class="card-text text-muted "><i class="fa-solid fa-location-dot"></i> Street Name, Barangay, Zamboanga City</p>
                                    <span class="opennow">Open Now</span>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col">
                        <a href="#" class="card-link text-decoration-none" data-bs-toggle="modal" data-bs-target="#invitestall">
                            <div class="card" style="position: relative;">
                                <img src="assets/images/foodpark.jpg" class="card-img-top" alt="...">
                                <i class="fa-solid fa-ellipsis-vertical ellipsis-icon"></i>
                                <div class="card-body">
                                    <h5 class="card-title">Food Park Name</h5>
                                    <p class="card-text text-muted "><i class="fa-solid fa-location-dot"></i> Street Name, Barangay, Zamboanga City</p>
                                    <span class="opennow">Open Now</span>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col">
                        <a href="#" class="card-link text-decoration-none" data-bs-toggle="modal" data-bs-target="#invitestall">
                            <div class="card" style="position: relative;">
                                <img src="assets/images/foodpark.jpg" class="card-img-top" alt="...">
                                <i class="fa-solid fa-ellipsis-vertical ellipsis-icon"></i>
                                <div class="card-body">
                                    <h5 class="card-title">Food Park Name</h5>
                                    <p class="card-text text-muted "><i class="fa-solid fa-location-dot"></i> Street Name, Barangay, Zamboanga City</p>
                                    <span class="opennow">Open Now</span>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col">
                        <a href="#" class="card-link text-decoration-none" data-bs-toggle="modal" data-bs-target="#invitestall">
                            <div class="card" style="position: relative;">
                                <img src="assets/images/foodpark.jpg" class="card-img-top" alt="...">
                                <i class="fa-solid fa-ellipsis-vertical ellipsis-icon"></i>
                                <div class="card-body">
                                    <h5 class="card-title">Food Park Name</h5>
                                    <p class="card-text text-muted "><i class="fa-solid fa-location-dot"></i> Street Name, Barangay, Zamboanga City</p>
                                    <span class="opennow">Open Now</span>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>  
            </div>
        </div>
    </div>
</div>

<!-- Select Food Stall -->
<div class="modal fade" id="selectstall" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 75%;">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Select which stall the item belong</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body modal-scrollable">
                <div class="row row-cols-1 row-cols-md-3 g-3">
                    <div class="col">
                        <a href="addproduct.php" class="card-link text-decoration-none bg-white">
                            <div class="card" style="position: relative;">
                                <img src="assets/images/stall1.jpg" class="card-img-top" alt="...">
                                <button class="add"><i class="fa-regular fa-heart"></i></button>
                                <div class="card-body">
                                    <div class="d-flex gap-2 align-items-center">
                                    <p class="card-text text-muted m-0">Category</p>
                                    <span class="dot text-muted"></span>
                                    <p class="card-text text-muted m-0">Category</p>
                                </div>
                                    <h5 class="card-title my-2">Food Stall Name</h5>
                                    <div class="d-flex justify-content-between">
                                        <p class="card-text text-muted m-0">Description</p>
                                        <span style="color:#6A9C89;"><i class="fa-solid fa-heart"></i> 200</span>
                                    </div>
                                    <div class="mt-2">
                                        <span class="discount">With Promo</span>
                                        <span class="newopen">New Open</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col">
                        <a href="addproduct.php" class="card-link text-decoration-none bg-white">
                            <div class="card" style="position: relative;">
                                <img src="assets/images/stall1.jpg" class="card-img-top" alt="...">
                                <button class="add"><i class="fa-regular fa-heart"></i></button>
                                <div class="card-body">
                                    <div class="d-flex gap-2 align-items-center">
                                    <p class="card-text text-muted m-0">Category</p>
                                    <span class="dot text-muted"></span>
                                    <p class="card-text text-muted m-0">Category</p>
                                </div>
                                    <h5 class="card-title my-2">Food Stall Name</h5>
                                    <div class="d-flex justify-content-between">
                                        <p class="card-text text-muted m-0">Description</p>
                                        <span style="color:#6A9C89;"><i class="fa-solid fa-heart"></i> 200</span>
                                    </div>
                                    <div class="mt-2">
                                        <span class="discount">With Promo</span>
                                        <span class="newopen">New Open</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col">
                        <a href="addproduct.php" class="card-link text-decoration-none bg-white">
                            <div class="card" style="position: relative;">
                                <img src="assets/images/stall1.jpg" class="card-img-top" alt="...">
                                <button class="add"><i class="fa-regular fa-heart"></i></button>
                                <div class="card-body">
                                    <div class="d-flex gap-2 align-items-center">
                                    <p class="card-text text-muted m-0">Category</p>
                                    <span class="dot text-muted"></span>
                                    <p class="card-text text-muted m-0">Category</p>
                                </div>
                                    <h5 class="card-title my-2">Food Stall Name</h5>
                                    <div class="d-flex justify-content-between">
                                        <p class="card-text text-muted m-0">Description</p>
                                        <span style="color:#6A9C89;"><i class="fa-solid fa-heart"></i> 200</span>
                                    </div>
                                    <div class="mt-2">
                                        <span class="discount">With Promo</span>
                                        <span class="newopen">New Open</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col">
                        <a href="addproduct.php" class="card-link text-decoration-none bg-white">
                            <div class="card" style="position: relative;">
                                <img src="assets/images/stall1.jpg" class="card-img-top" alt="...">
                                <button class="add"><i class="fa-regular fa-heart"></i></button>
                                <div class="card-body">
                                    <div class="d-flex gap-2 align-items-center">
                                    <p class="card-text text-muted m-0">Category</p>
                                    <span class="dot text-muted"></span>
                                    <p class="card-text text-muted m-0">Category</p>
                                </div>
                                    <h5 class="card-title my-2">Food Stall Name</h5>
                                    <div class="d-flex justify-content-between">
                                        <p class="card-text text-muted m-0">Description</p>
                                        <span style="color:#6A9C89;"><i class="fa-solid fa-heart"></i> 200</span>
                                    </div>
                                    <div class="mt-2">
                                        <span class="discount">With Promo</span>
                                        <span class="newopen">New Open</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Placed Order with Cash Paymenyt -->
<div class="modal fade" id="ifcash" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <div class="d-flex justify-content-end">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="text-center">
                    <i class="fa-regular fa-face-smile mb-3" style="color: #CD5C08; font-size: 80px"></i><br>
                    <span>Thank you for your order!</span>
                    <h5 class="fw-bold mt-2 mb-4">Your Order ID is <span style="color: #CD5C08;">0001</span></h5>
                    <p class="mb-3">Please proceed to each stall with this Order ID to complete your payment. Once payment is confirmed, your order will be in preparation queue. </p>
                    <span>For more details about your order, go to Purchase.</span>
                </div>
                <div class="text-center mt-4">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Purchase</button>
                </div>
                <br>
            </div>
        </div>
    </div>
</div>

<!-- Placed Order with Online Paymenyt -->
<div class="modal fade" id="ifcashless" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <div class="d-flex justify-content-end">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="text-center">
                    <i class="fa-regular fa-face-smile mb-3" style="color: #CD5C08; font-size: 80px"></i><br>
                    <span>Thank you for your order!</span>
                    <h5 class="fw-bold mt-2 mb-4">Your Order ID is <span style="color: #CD5C08;">0000</span></h5>
                    <p class="mb-3"> Your order at each stall is now in preparation queue, you will be notified when your items are ready for pickup.</p>
                    <span>For more details about your order, go to Purchase.</span>
                </div>
                <div class="text-center mt-4">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Purchase</button>
                </div>
                <br>
            </div>
        </div>
    </div>
</div>
<!--id="purchaseButton
<script>
    async function fetchPaymentLink() {
        try {
            const response = await fetch('paymongo.php');
            const data = await response.json();

            if (data.checkout_url) {
                document.getElementById('purchaseButton').onclick = function () {
                    window.location.href = data.checkout_url;
                };
            } else {
                console.error('Error fetching payment link:', data.error);
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }
    window.onload = fetchPaymentLink;
</script>"-->

<!-- Placed Order as scheduled with Online Paymenyt -->
<div class="modal fade" id="ifscheduled" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <div class="d-flex justify-content-end">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="text-center">
                    <i class="fa-solid fa-clock-rotate-left mb-3" style="color: #CD5C08; font-size: 80px"></i><br>
                    <span>Order Scheduled!</span>
                    <h5 class="fw-bold mt-2 mb-4">Your Order ID is <span style="color: #CD5C08;">0000</span></h5>
                    <p class="mb-3">Your order is scheduled for October 15, 2024 at 1:00 PM. You will receive a notification once your order is being prepared at that time.</p>
                    <span>For more details about your order, go to Purchase.</span>
                </div>
                <div class="text-center mt-4">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Purchase</button>
                </div>
                <br>
            </div>
        </div>
    </div>
</div>

<!-- Account Activity Log -->
<div class="modal fade" id="activitylog" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-custom-width">
        <div class="modal-content">
            <div class="p-3 d-flex justify-content-between align-items-center">
                <h1 class="modal-title fs-5 fw-bold" id="staticBackdropLabel">Activity Log</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body modal-scrollable pt-0">
                <div>
                <div class="p-3 rounded-2 border mb-3">
                    <h6 class="mb-2">November 30, 2024</h6>
                    <div class="d-flex justify-content-between align-items-center actlog">
                        <div class="d-flex gap-3">
                            <img src="assets/images/profile.jpg" width="65" height="65" style="border-radius: 50%">
                            <div>
                                <p class="m-0">Naila Haliluddin searched on GitGud</p>
                                <p class="small text-muted m-0">"cheese"</p>
                                <p class="small text-muted m-0">7:43 PM</p>
                            </div>
                        </div>
                        <i class="fa-solid fa-trash rename" style="cursor: pointer;"></i>
                    </div>
                    <div class="d-flex justify-content-between align-items-center actlog">
                        <div class="d-flex gap-3">
                            <img src="assets/images/profile.jpg" width="65" height="65" style="border-radius: 50%">
                            <div>
                                <p class="m-0">Naila Haliluddin visited on GitGud</p>
                                <p class="small text-muted m-0">"Stall Name"</p>
                                <p class="small text-muted m-0">7:05 PM</p>
                            </div>
                        </div>
                        <i class="fa-solid fa-trash rename" style="cursor: pointer;"></i>
                    </div>
                </div>
                <div class="p-3 rounded-2 border mb-3">
                    <h6 class="mb-2">November 30, 2024</h6>
                    <div class="d-flex justify-content-between align-items-center actlog">
                        <div class="d-flex gap-3">
                            <img src="assets/images/profile.jpg" width="65" height="65" style="border-radius: 50%">
                            <div>
                                <p class="m-0">Naila Haliluddin searched on GitGud</p>
                                <p class="small text-muted m-0">"cheese"</p>
                                <p class="small text-muted m-0">7:43 PM</p>
                            </div>
                        </div>
                        <i class="fa-solid fa-trash rename" style="cursor: pointer;"></i>
                    </div>
                    <div class="d-flex justify-content-between align-items-center actlog">
                        <div class="d-flex gap-3">
                            <img src="assets/images/profile.jpg" width="65" height="65" style="border-radius: 50%">
                            <div>
                                <p class="m-0">Naila Haliluddin visited on GitGud</p>
                                <p class="small text-muted m-0">"Stall Name"</p>
                                <p class="small text-muted m-0">7:05 PM</p>
                            </div>
                        </div>
                        <i class="fa-solid fa-trash rename" style="cursor: pointer;"></i>
                    </div>
                </div>
                <div class="p-3 rounded-2 border mb-3">
                    <h6 class="mb-2">November 30, 2024</h6>
                    <div class="d-flex justify-content-between align-items-center actlog">
                        <div class="d-flex gap-3">
                            <img src="assets/images/profile.jpg" width="65" height="65" style="border-radius: 50%">
                            <div>
                                <p class="m-0">Naila Haliluddin searched on GitGud</p>
                                <p class="small text-muted m-0">"cheese"</p>
                                <p class="small text-muted m-0">7:43 PM</p>
                            </div>
                        </div>
                        <i class="fa-solid fa-trash rename" style="cursor: pointer;"></i>
                    </div>
                    <div class="d-flex justify-content-between align-items-center actlog">
                        <div class="d-flex gap-3">
                            <img src="assets/images/profile.jpg" width="65" height="65" style="border-radius: 50%">
                            <div>
                                <p class="m-0">Naila Haliluddin visited on GitGud</p>
                                <p class="small text-muted m-0">"Stall Name"</p>
                                <p class="small text-muted m-0">7:05 PM</p>
                            </div>
                        </div>
                        <i class="fa-solid fa-trash rename" style="cursor: pointer;"></i>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Confirm Payment -->
<div class="modal fade" id="confirmpayment" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <div class="d-flex justify-content-end">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="text-center">
                    <h4 class="fw-bold mb-4"><i class="fa-solid fa-circle-question me-2"></i> Confirm Payment</h4>
                    <p class="mb-2">You are about to confirm the payment for Order ID 0000.<br>Are you sure?</p>
                    <ul class="text-muted small text-start">
                        <li>Please ensure that the total amount of PHP 250.00 has been collected.</li>
                        <li>The order will be in preparation queue once confirmed, please inform the customer.</li>
                    </ul>
                    <div class="mt-5 mb-3">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary">Confirm Payment</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Cashier -->
<div class="modal fade" id="addcashier" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content p-4">
            <div class="modal-header p-0 border-0 m-0">
                <h5 class="m-0">Add Cashier</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 m-0">
                <form action="#" class="form w-100 border-0 p-0" method="POST">
                    <div class="input-group">
                        <label for="firstname">Name</label>
                        <input type="text" name="name" id="name" placeholder="Enter name of cashier" />
                    </div>
                    <div class="input-group">
                        <label for="password">Password</label>
                        <input type="password" name="password" id="password" placeholder="Enter password" />
                    </div>
                    <div class="input-group">
                        <label for="shiftstart">Shift Start Time</label>
                        <input type="time" name="shiftstart" id="shiftstart">
                    </div>
                    <div class="input-group">
                        <label for="shiftend">Shift End Time</label>
                        <input type="time" name="shiftend" id="shiftend">
                    </div>
                    <input type="submit" value="Add Cashier" class="button" />
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Cashier -->
<div class="modal fade" id="editcashier" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content p-4">
            <div class="modal-header p-0 border-0 m-0">
                <h5 class="m-0">Edit Cashier</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 m-0">
                <form action="#" class="form w-100 border-0 p-0" method="POST">
                    <!-- Assuming cashier ID is passed for identification -->
                    <input type="hidden" name="cashier_id" id="cashier_id" value="1"> 
                    <div class="input-group">
                        <label for="name">Name</label>
                        <input type="text" name="name" id="name" value="John Doe" placeholder="Enter name of cashier" />
                    </div>
                    <div class="input-group">
                        <label for="password">Password</label>
                        <input type="password" name="password" id="password" value="123456" placeholder="Enter password" />
                    </div>
                    <div class="input-group">
                        <label for="shiftstart">Shift Start Time</label>
                        <input type="time" name="shiftstart" id="shiftstart" value="08:00">
                    </div>
                    <div class="input-group">
                        <label for="shiftend">Shift End Time</label>
                        <input type="time" name="shiftend" id="shiftend" value="16:00">
                    </div>
                    <input type="submit" value="Update Cashier" class="button" />
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Cashier -->
<div class="modal fade" id="deletecashier" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <div class="d-flex justify-content-end">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="text-center">
                    <h4 class="fw-bold mb-4"><i class="fa-solid fa-circle-exclamation"></i> Delete Cashier</h4>
                    <span>You are about to delete this Cashier.<br>Are you sure?</span>
                    <div class="mt-5 mb-3">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Change Password Modal -->
<div class="modal fade" id="changepassword" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 40%">
        <div class="modal-content">
            <div class="modal-body">
                <div class="d-flex justify-content-end">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="">
                    <h4 class="fw-bold">Change Password</h4>
                    <p class="small m-0 my-3">Your password must be at least 8 characters.</p>

                    <form id="changePasswordForm" action="classes/change_password.php" method="POST">
                        <div class="form-floating mb-3">
                            <input type="password" class="form-control" name="currentpassword" placeholder="Current Password" required>
                            <label for="currentpassword">Current Password</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="password" class="form-control" name="newpassword" placeholder="New Password" required>
                            <label for="newpassword">New Password</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="password" class="form-control" name="retypepassword" placeholder="Re-type New Password" required>
                            <label for="retypepassword">Re-type New Password</label>
                        </div>
                        <a href="resetpassword.php" class="text-decoration-none" style=" color: #CD5C08;">Forgot Password?</a>
                        <div class="form-check mt-3 mb-5">
                            <input class="form-check-input" type="checkbox" name="logout_other_devices" value="1" id="flexCheckDefault" checked>
                            <label class="form-check-label" for="flexCheckDefault">Log out of other devices. Choose this if someone else used your account.</label>
                        </div>
                        <input type="submit" value="Change Password" class="button" />
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Check for message -->
<script>
    // Check for session messages and open the modal
    <?php if (isset($_SESSION['error'])): ?>
        alert("<?php echo $_SESSION['error']; ?>");
        // Open the modal
        var myModal = new bootstrap.Modal(document.getElementById('changepassword'));
        myModal.show();
        <?php unset($_SESSION['error']); ?>
    <?php elseif (isset($_SESSION['success'])): ?>
        alert("<?php echo $_SESSION['success']; ?>");
        // Open the modal
        var myModal = new bootstrap.Modal(document.getElementById('changepassword'));
        myModal.show();
        
        // Save the user session as xdata
        var xdata = "<?php echo isset($_SESSION['user_session']) ? $_SESSION['user_session'] : ''; ?>";
        localStorage.setItem('xdata', xdata); // Store xdata in local storage

        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
</script>

<!-- Delete Account -->
<div class="modal fade" id="deleteaccount" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="d-flex justify-content-end">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="text-center">
                    <h4 class="fw-bold">Delete Account</h4>
                    <p class="m-0 my-3">Deleting your account will remove all of your information from our database. This cannot be undone.</p>

                    <div class="form-floating mb-5">
                        <input type="password" class="form-control" id="currentpassword" placeholder="Current Password (Updated 10/21/2023">
                        <label for="currentpassword">To confirm this, type "DELETE"</label>
                    </div>
                    <input type="submit" value="Delete Account" class="button" />
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Report -->
<div class="modal fade" id="report" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
<div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-body">
        <div class="d-flex justify-content-end">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="text-center">
            <h4 class="fw-bold mb-4">Why are you reporting this?</h4>
            <div class="form-floating m-0">
                <textarea class="form-control" placeholder="Reason" id="reason" oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'"></textarea>
                <label for="reason">Reason</label>
            </div>
            <div class="mt-4 mb-3">
                <input type="submit" value="Submit" class="button" />
            </div>
        </div>
      </div>
    </div>
  </div>
</div>


 <!-- Pending Cashless Approval 
    <div class="modal fade" id="pendingcashless" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <br>
                    <div class="text-center">
                        <i class="fa-solid fa-wallet mb-4"  style="color: #CD5C08; font-size: 80px"></i>
                        <p class="mb-4">Your selected payment method (GCash) is pending approval. Please note that the admin will review and set up the API before this payment method can be used. You will receive a notification once an action has been taken.</p>
                    </div>
                    <div class="d-flex justify-content-center mt-4">
                        <button type="button" class="button w-25" onclick="window.location.href='stallpage.php';">OK</button>
                    </div>
                    <br>
                </div>
            </div>
        </div>
    </div>-->