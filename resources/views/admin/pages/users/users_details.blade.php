<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Details</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            background: #f4f7fe;
            font-family: 'Poppins', sans-serif;
        }

        .main-container {
            padding: 40px 20px;
        }

        .top-banner {
            background: linear-gradient(135deg, #2563eb, #1e40af);
            border-radius: 30px;
            overflow: hidden;
            position: relative;
            padding: 50px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.12);
        }

        .profile-img {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            object-fit: cover;
            border: 6px solid rgba(255, 255, 255, 0.4);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }

        .profile-name {
            font-size: 32px;
            font-weight: 700;
            color: white;
            margin-bottom: 10px;
        }

        .custom-badge {
            background: rgba(255, 255, 255, 0.15);
            color: white;
            padding: 10px 20px;
            border-radius: 50px;
            font-size: 14px;
            font-weight: 600;
            backdrop-filter: blur(10px);
        }

        .details-card {
            background: white;
            border-radius: 25px;
            padding: 30px;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.06);
            height: 100%;
            transition: 0.3s ease;
            border: none;
        }

        .details-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.12);
        }

        .card-title-custom {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: start;
            padding: 15px 0;
            border-bottom: 1px solid #edf2f7;
            gap: 15px;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: #64748b;
        }

        .info-value {
            font-weight: 600;
            color: #111827;
            text-align: right;
        }

        .download-btn {
            background: linear-gradient(135deg, #0ea5e9, #0284c7);
            color: white;
            border: none;
            padding: 14px 30px;
            border-radius: 50px;
            font-weight: 600;
            transition: 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .download-btn:hover {
            transform: translateY(-3px);
            color: white;
            box-shadow: 0 10px 20px rgba(14, 165, 233, 0.3);
        }

        .back-btn {
            background: white;
            color: #1e40af;
            padding: 12px 28px;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            transition: 0.3s ease;
        }

        .back-btn:hover {
            background: #f8fafc;
            transform: translateY(-3px);
            color: #1e40af;
        }

        @media(max-width: 768px) {
            .top-banner {
                padding: 30px;
                text-align: center;
            }

            .info-row {
                flex-direction: column;
            }

            .info-value {
                text-align: left;
            }

            .profile-name {
                font-size: 24px;
            }
        }
    </style>
</head>

<body>

    <div class="main-container">
        <div class="container-fluid">

            <div class="content">
                <div class="container-fluid">

                    <!-- Page Title -->
<div class="row mb-4">
    <div class="col-12">

        <div class="card border-0 shadow-lg rounded-4 overflow-hidden">

            <div class="card-body bg-primary text-white p-5">

                <div class="d-flex justify-content-between align-items-start flex-wrap gap-4">

                    <!-- Left Side -->
                    <div class="d-flex align-items-center gap-4 flex-wrap">

                        @if ($users->photo)

                            <img src="{{ asset('upload/user_images/' . $users->photo) }}"
                                 class="rounded-circle border border-4 border-white shadow"
                                 width="120"
                                 height="120"
                                 style="object-fit: cover;">

                        @else

                            <img src="https://ui-avatars.com/api/?name={{ $users->name }}"
                                 class="rounded-circle border border-4 border-white shadow"
                                 width="120"
                                 height="120">

                        @endif

                        <!-- User Info -->
                        <div>

                            <h2 class="fw-bold text-white mb-2">

                                {{ $users->name }} {{ $users->lastname }}

                            </h2>

                            <div class="d-flex align-items-center gap-2 flex-wrap mb-3">

                                <span class="badge bg-light text-dark px-3 py-2 rounded-pill">

                                    <i class="fas fa-user-shield me-1"></i>

                                    {{ ucfirst($users->role) }}

                                </span>

                                <span class="badge bg-success px-3 py-2 rounded-pill">

                                    <i class="fas fa-check-circle me-1"></i>

                                    {{ ucfirst($users->status) }}

                                </span>

                            </div>

                            <!-- Family Members -->
                            <div>

                                <h6 class="fw-bold text-white mb-2">

                                    <i class="fas fa-users me-2"></i>

                                    اعضای فامیل

                                </h6>

                                <div class="d-flex flex-wrap gap-2">

                                    @if ($users->familyMember)

                                        @foreach (explode(',', $users->familyMember->name) as $member)

                                            <span class="badge bg-light text-dark px-3 py-2 rounded-pill shadow-sm">

                                                <i class="fas fa-user me-1 text-primary"></i>

                                                {{ trim($member) }}

                                            </span>

                                        @endforeach

                                    @else

                                        <span class="badge bg-danger px-3 py-2 rounded-pill">

                                            اعضای فامیل موجود نیست

                                        </span>

                                    @endif

                                </div>

                            </div>

                        </div>

                    </div>

                    <!-- Right Side -->
                    <div>

                        <a href="{{ route('all.users') }}"
                           class="btn btn-light rounded-pill px-4 shadow-sm">

                            <i class="fas fa-arrow-left me-2"></i>

                            Back

                        </a>

                    </div>

                </div>

            </div>

        </div>

    </div>
</div>

         

                    <div class="row g-4">

                        <!-- Personal Information -->
                        <div class="col-lg-6">
                            <div class="card border-0 shadow rounded-4 h-100">
                                <div class="card-header bg-white border-0 pt-4 px-4">
                                    <h4 class="fw-bold text-primary mb-0">
                                        <i class="fas fa-user-circle me-2"></i>
                                        Personal Information
                                    </h4>
                                </div>

                                <div class="card-body px-4 pb-4">
                                    <div class="info-item">
                                        <span>Full Name</span>
                                        <strong>{{ $users->name }} {{ $users->lastname }}</strong>
                                    </div>

                                    <div class="info-item">
                                        <span>Father Name</span>
                                        <strong>{{ $users->father_name }}</strong>
                                    </div>

                                    <div class="info-item">
                                        <span>Grandfather Name</span>
                                        <strong>{{ $users->grandfather_name }}</strong>
                                    </div>

                                    <div class="info-item">
                                        <span>Gender</span>
                                        <strong>{{ ucfirst($users->gender) }}</strong>
                                    </div>

                                    <div class="info-item">
                                        <span>Birth Date</span>
                                        <strong>{{ $users->birth_date }}</strong>
                                    </div>

                                    <div class="info-item">
                                        <span>Marital Status</span>
                                        <strong>{{ ucfirst($users->marital_status) }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="col-lg-6">
                            <div class="card border-0 shadow rounded-4 h-100">
                                <div class="card-header bg-white border-0 pt-4 px-4">
                                    <h4 class="fw-bold text-success mb-0">
                                        <i class="fas fa-address-book me-2"></i>
                                        Contact Information
                                    </h4>
                                </div>

                                <div class="card-body px-4 pb-4">
                                    <div class="info-item">
                                        <span>Email</span>
                                        <strong>{{ $users->email }}</strong>
                                    </div>

                                    <div class="info-item">
                                        <span>Phone</span>
                                        <strong>{{ $users->phone }}</strong>
                                    </div>

                                    <div class="info-item">
                                        <span>Permanent Address</span>
                                        <strong>{{ $users->permanent_address }}</strong>
                                    </div>

                                    <div class="info-item">
                                        <span>Current Address</span>
                                        <strong>{{ $users->current_address }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Education & Job -->
                        <div class="col-lg-6">
                            <div class="card border-0 shadow rounded-4 h-100">
                                <div class="card-header bg-white border-0 pt-4 px-4">
                                    <h4 class="fw-bold text-warning mb-0">
                                        <i class="fas fa-graduation-cap me-2"></i>
                                        Education & Work
                                    </h4>
                                </div>

                                <div class="card-body px-4 pb-4">
                                    <div class="info-item">
                                        <span>Education Level</span>
                                        <strong>{{ $users->education_level }}</strong>
                                    </div>

                                    <div class="info-item">
                                        <span>Job</span>
                                        <strong>{{ $users->job }}</strong>
                                    </div>

                                    <div class="info-item">
                                        <span>Work Place</span>
                                        <strong>{{ $users->work_place }}</strong>
                                    </div>

                                    <div class="info-item">
                                        <span>Economic Status</span>
                                        <strong>{{ $users->economic_status }}</strong>
                                    </div>

                                    <div class="info-item">
                                        <span>Family Members</span>
                                        <strong>{{ $users->family_members }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Membership Details -->
                        <div class="col-lg-6">
                            <div class="card border-0 shadow rounded-4 h-100">
                                <div class="card-header bg-white border-0 pt-4 px-4">
                                    <h4 class="fw-bold text-danger mb-0">
                                        <i class="fas fa-id-card me-2"></i>
                                        Membership Details
                                    </h4>
                                </div>

                                <div class="card-body px-4 pb-4">
                                    <div class="info-item">
                                        <span>Member Type</span>
                                        <strong>{{ ucfirst($users->member_type) }}</strong>
                                    </div>

                                    <div class="info-item">
                                        <span>Monthly Fee</span>
                                        <strong>${{ $users->monthly_fee }}</strong>
                                    </div>

                                    <div class="info-item">
                                        <span>Ethnic Branch</span>
                                        <strong>{{ $users->ethnic_branch }}</strong>
                                    </div>

                                    <div class="info-item">
                                        <span>Representative Name</span>
                                        <strong>{{ $users->representative_name }}</strong>
                                    </div>

                                    <div class="info-item">
                                        <span>Register Date</span>
                                        <strong>{{ $users->register_date }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Documents -->
                        <div class="col-12">
                            <div class="card border-0 shadow rounded-4">
                                <div class="card-header bg-white border-0 pt-4 px-4">
                                    <h4 class="fw-bold text-info mb-0">
                                        <i class="fas fa-file-alt me-2"></i>
                                        User Documents
                                    </h4>
                                </div>

                                <div class="card-body p-4">
                                    @if ($users->documents)
                                        <a href="{{ asset('upload/user_documents/' . $users->documents) }}"
                                            target="_blank" class="btn btn-info rounded-pill px-4 py-2">
                                            <i class="fas fa-download me-2"></i>
                                            Download Document
                                        </a>
                                    @else
                                        <div class="alert alert-warning rounded-4 mb-0">
                                            No document uploaded.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <style>
                .info-item {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    padding: 15px 0;
                    border-bottom: 1px dashed #dee2e6;
                    gap: 15px;
                }

                .info-item:last-child {
                    border-bottom: none;
                }

                .info-item span {
                    color: #6c757d;
                    font-weight: 600;
                    min-width: 150px;
                }

                .info-item strong {
                    color: #212529;
                    text-align: right;
                }

                .card {
                    transition: 0.3s ease;
                }

                .card:hover {
                    transform: translateY(-5px);
                    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12) !important;
                }

                @media(max-width: 768px) {
                    .info-item {
                        flex-direction: column;
                        align-items: start;
                    }

                    .info-item strong {
                        text-align: left;
                    }
                }
            </style>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
