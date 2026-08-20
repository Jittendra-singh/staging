@extends('layouts.frontend')

@push('style')
<style>
    /* Styling for the new Waitlist Management mockup */
    .waitlist-page-container {
        font-family: 'Inter', sans-serif;
        background-color: #f8fafc;
        min-height: 100vh;
        padding-top: 30px;
        padding-bottom: 60px;
    }
    .page-title {
        color: #1e3a8a; /* dark blue */
        font-weight: 700;
        font-size: 28px;
        margin-bottom: 4px;
    }
    .page-subtitle {
        color: #475569;
        font-weight: 600;
        font-size: 16px;
        margin-bottom: 12px;
    }
    .breadcrumb-text {
        font-size: 13px;
        color: #64748b;
        font-weight: 500;
    }
    .breadcrumb-text a {
        color: #64748b;
        text-decoration: none;
    }
    .breadcrumb-text span {
        margin: 0 6px;
    }
    .breadcrumb-text .active {
        color: #1e293b;
        font-weight: 600;
    }
    
    .card-custom {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    
    .capacity-card {
        padding: 24px;
        height: 100%;
    }
    .capacity-title {
        font-size: 16px;
        font-weight: 700;
        color: #1e293b;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }
    .capacity-title i {
        color: #3b82f6;
    }
    .capacity-stat {
        display: flex;
        justify-content: space-between;
        font-size: 14px;
        color: #475569;
        font-weight: 500;
        margin-bottom: 16px;
    }
    .capacity-stat .value {
        color: #3b82f6;
        font-weight: 700;
    }
    .capacity-divider {
        height: 1px;
        background: #e2e8f0;
        margin: 16px 0;
    }
    .waitlist-stat {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        font-weight: 600;
        color: #1e293b;
    }
    .waitlist-stat i {
        color: #ef4444;
    }
    
    .filter-card {
        padding: 24px;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    
    .form-control-custom, .form-select-custom {
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        font-size: 14px;
        color: #475569;
        padding: 10px 16px;
    }
    
    .btn-search {
        background-color: #2563eb;
        color: white;
        border-radius: 8px;
        padding: 8px 20px;
        font-weight: 500;
        border: none;
    }
    .btn-export {
        background-color: #1e3a8a;
        color: white;
        border-radius: 8px;
        padding: 8px 20px;
        font-weight: 500;
        border: none;
    }
    
    /* Table Styles */
    .table-container {
        margin-top: 24px;
        overflow: hidden;
    }
    .table-custom {
        width: 100%;
        border-collapse: collapse;
    }
    .table-custom th {
        background-color: #f8fafc;
        color: #64748b;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 16px 24px;
        border-bottom: 1px solid #e2e8f0;
        text-align: left;
    }
    .table-custom td {
        padding: 16px 24px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 14px;
        color: #475569;
        vertical-align: middle;
    }
    .table-custom tr:last-child td {
        border-bottom: none;
    }
    
    .volunteer-cell {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 700;
        color: white;
    }
    .volunteer-name {
        font-weight: 600;
        color: #1e293b;
    }
    
    .badge-ticket {
        background-color: #f1f5f9;
        color: #475569;
        font-size: 12px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 20px;
    }
    .badge-ticket.student {
        background-color: #eff6ff;
        color: #3b82f6;
    }
    
    .badge-status {
        font-size: 12px;
        font-weight: 600;
        padding: 4px 12px;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .badge-status::before {
        content: '';
        display: inline-block;
        width: 6px;
        height: 6px;
        border-radius: 50%;
    }
    .badge-status.waiting {
        background-color: #fee2e2;
        color: #b91c1c;
    }
    .badge-status.waiting::before {
        background-color: #ef4444;
    }
    .badge-status.registered {
        background-color: #dcfce3;
        color: #166534;
    }
    .badge-status.registered::before {
        background-color: #22c55e;
    }
    
    .pagination-footer {
        padding: 16px 24px;
        border-top: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .pagination-text {
        font-size: 13px;
        color: #64748b;
    }
    .pagination-controls {
        display: flex;
        gap: 4px;
    }
    .page-btn {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #e2e8f0;
        background: white;
        border-radius: 6px;
        color: #475569;
        font-size: 13px;
        font-weight: 500;
        text-decoration: none;
    }
    .page-btn.active {
        background-color: #eff6ff;
        color: #2563eb;
        border-color: #bfdbfe;
    }
</style>
@endpush

@section('content')
<div class="waitlist-page-container pt-5 mt-5">
    <div class="container pt-3">
        
        <!-- Header Section -->
        <div class="mb-4">
            <h1 class="page-title">Waitlist Management</h1>
            <div class="page-subtitle">Annual Beach Cleanup</div>
            <div class="breadcrumb-text">
                <a href="{{ route('front-dashboard') }}">Dashboard</a> <span>&gt;</span>
                <a href="#">Cleanups I'm Hosting</a> <span>&gt;</span>
                <span class="active">Waitlist Management</span>
            </div>
        </div>
        
        <!-- Top Cards -->
        <div class="row g-4 mb-4">
            <!-- Capacity Card -->
            <div class="col-md-4">
                <div class="card-custom capacity-card">
                    <div class="capacity-title">
                        Event Capacity <i class="fa-solid fa-user-group"></i>
                    </div>
                    <div class="capacity-stat">
                        <span>Registered</span>
                        <span class="value">150 / 150</span>
                    </div>
                    <div class="capacity-divider"></div>
                    <div class="waitlist-stat">
                        <i class="fa-solid fa-hourglass-half"></i> <span><strong style="color: #ef4444;">42</strong> volunteers on waitlist</span>
                    </div>
                </div>
            </div>
            
            <!-- Filters Card -->
            <div class="col-md-8">
                <div class="card-custom filter-card">
                    <div class="row g-3 mb-3">
                        <div class="col-md-5">
                            <input type="text" class="form-control form-control-custom w-100" placeholder="Search Volunteers by name or email id">
                        </div>
                        <div class="col-md-4">
                            <select class="form-select form-select-custom w-100">
                                <option>Annual Beach Clean...</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select form-select-custom w-100">
                                <option>All Ticket Types</option>
                            </select>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <button class="btn-search"><i class="fa-solid fa-search me-1"></i> Search</button>
                        <button class="btn-export"><i class="fa-solid fa-download me-1"></i> Export CSV</button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Table Card -->
        <div class="card-custom table-container">
            <div class="table-responsive">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>VOLUNTEER</th>
                            <th>EMAIL</th>
                            <th>TICKET TYPE</th>
                            <th>STATUS</th>
                            <th>JOINED WAITLIST</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Row 1 -->
                        <tr>
                            <td>
                                <div class="volunteer-cell">
                                    <img src="https://i.pravatar.cc/150?img=5" class="avatar" style="object-fit: cover;">
                                    <span class="volunteer-name">Sarah Jenkins</span>
                                </div>
                            </td>
                            <td>sarah.j@example.com</td>
                            <td><span class="badge-ticket">General Admission</span></td>
                            <td><span class="badge-status waiting">Waiting</span></td>
                            <td>Oct 12, 2023 - 09:15 AM</td>
                        </tr>
                        <!-- Row 2 -->
                        <tr>
                            <td>
                                <div class="volunteer-cell">
                                    <div class="avatar" style="background-color: #bfdbfe; color: #1e3a8a;">MR</div>
                                    <span class="volunteer-name">Marcus Rodriguez</span>
                                </div>
                            </td>
                            <td>m.rod@example.com</td>
                            <td><span class="badge-ticket student">Student</span></td>
                            <td><span class="badge-status waiting">Waiting</span></td>
                            <td>Oct 12, 2023 - 10:30 AM</td>
                        </tr>
                        <!-- Row 3 -->
                        <tr>
                            <td>
                                <div class="volunteer-cell">
                                    <div class="avatar" style="background-color: #f1f5f9; color: #64748b;"><i class="fa-regular fa-user"></i></div>
                                    <span class="volunteer-name">David Chen</span>
                                </div>
                            </td>
                            <td>dchen.design@example.com</td>
                            <td><span class="badge-ticket student" style="background-color: #e0e7ff; color: #4338ca;">VIP</span></td>
                            <td><span class="badge-status waiting">Waiting</span></td>
                            <td>Oct 12, 2023 - 11:45 AM</td>
                        </tr>
                        <!-- Row 4 -->
                        <tr>
                            <td>
                                <div class="volunteer-cell">
                                    <div class="avatar" style="background-color: #bbf7d0; color: #166534;">AL</div>
                                    <span class="volunteer-name">Amanda Lewis</span>
                                </div>
                            </td>
                            <td>amanda.l@example.com</td>
                            <td><span class="badge-ticket">General Admission</span></td>
                            <td><span class="badge-status registered">Registered</span></td>
                            <td>Oct 12, 2023 - 01:20 PM</td>
                        </tr>
                        <!-- Row 5 -->
                        <tr>
                            <td>
                                <div class="volunteer-cell">
                                    <div class="avatar" style="background-color: #f1f5f9; color: #475569;">MS</div>
                                    <span class="volunteer-name">Michael Scott</span>
                                </div>
                            </td>
                            <td>m.scott@dundermifflin.com</td>
                            <td><span class="badge-ticket">General Admission</span></td>
                            <td><span class="badge-status waiting">Waiting</span></td>
                            <td>Oct 12, 2023 - 02:10 PM</td>
                        </tr>
                        <!-- Row 6 -->
                        <tr>
                            <td>
                                <div class="volunteer-cell">
                                    <div class="avatar" style="background-color: #fbcfe8; color: #9d174d;">PB</div>
                                    <span class="volunteer-name">Pam Beesly</span>
                                </div>
                            </td>
                            <td>p.beesly@dundermifflin.com</td>
                            <td><span class="badge-ticket">General Admission</span></td>
                            <td><span class="badge-status waiting">Waiting</span></td>
                            <td>Oct 12, 2023 - 02:45 PM</td>
                        </tr>
                        <!-- Row 7 -->
                        <tr>
                            <td>
                                <div class="volunteer-cell">
                                    <div class="avatar" style="background-color: #bfdbfe; color: #1e3a8a;">JH</div>
                                    <span class="volunteer-name">Jim Halpert</span>
                                </div>
                            </td>
                            <td>j.halpert@dundermifflin.com</td>
                            <td><span class="badge-ticket student" style="background-color: #e0e7ff; color: #4338ca;">VIP</span></td>
                            <td><span class="badge-status waiting">Waiting</span></td>
                            <td>Oct 12, 2023 - 03:00 PM</td>
                        </tr>
                        <!-- Row 8 -->
                        <tr>
                            <td>
                                <div class="volunteer-cell">
                                    <div class="avatar" style="background-color: #f1f5f9; color: #475569;">DS</div>
                                    <span class="volunteer-name">Dwight Schrute</span>
                                </div>
                            </td>
                            <td>d.schrute@dundermifflin.com</td>
                            <td><span class="badge-ticket">General Admission</span></td>
                            <td><span class="badge-status waiting">Waiting</span></td>
                            <td>Oct 12, 2023 - 03:15 PM</td>
                        </tr>
                        <!-- Row 9 -->
                        <tr>
                            <td>
                                <div class="volunteer-cell">
                                    <div class="avatar" style="background-color: #bfdbfe; color: #1e3a8a;">AH</div>
                                    <span class="volunteer-name">Andy Bernard</span>
                                </div>
                            </td>
                            <td>a.bernard@cornell.edu</td>
                            <td><span class="badge-ticket student">Student</span></td>
                            <td><span class="badge-status waiting">Waiting</span></td>
                            <td>Oct 12, 2023 - 03:45 PM</td>
                        </tr>
                        <!-- Row 10 -->
                        <tr>
                            <td>
                                <div class="volunteer-cell">
                                    <div class="avatar" style="background-color: #e2e8f0; color: #334155;">AM</div>
                                    <span class="volunteer-name">Angela Martin</span>
                                </div>
                            </td>
                            <td>a.martin@dundermifflin.com</td>
                            <td><span class="badge-ticket">General Admission</span></td>
                            <td><span class="badge-status waiting">Waiting</span></td>
                            <td>Oct 12, 2023 - 04:00 PM</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!-- Pagination Footer -->
            <div class="pagination-footer">
                <div class="pagination-text">
                    Showing <strong>1</strong> to <strong>10</strong> of <strong>42</strong> volunteers
                </div>
                <div class="pagination-controls">
                    <a href="#" class="page-btn"><i class="fa-solid fa-chevron-left" style="font-size: 10px;"></i></a>
                    <a href="#" class="page-btn active">1</a>
                    <a href="#" class="page-btn">2</a>
                    <a href="#" class="page-btn">3</a>
                    <a href="#" class="page-btn border-0">...</a>
                    <a href="#" class="page-btn">5</a>
                    <a href="#" class="page-btn"><i class="fa-solid fa-chevron-right" style="font-size: 10px;"></i></a>
                </div>
            </div>
        </div>
        
    </div>
</div>
@endsection
