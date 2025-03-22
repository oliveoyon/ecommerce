@extends('dashboard.layouts.admin-layout')
@section('title', 'Dashboard')
@section('content')
<section>
    <div class="container-fluid">
        <!-- Key Metrics Section -->
        <div class="dashboard-stats">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <div class="stat-box">
                        <div class="inner">
                            <h3>256</h3>
                            <p>Total Cases</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-briefcase"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 mb-3">
                    <div class="stat-box">
                        <div class="inner">
                            <h3>152</h3>
                            <p>Completed Interventions</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 mb-3">
                    <div class="stat-box">
                        <div class="inner">
                            <h3>104</h3>
                            <p>Pending Cases</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-exclamation-circle"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 mb-3">
                    <div class="stat-box">
                        <div class="inner">
                            <h3>45</h3>
                            <p>Upcoming Follow-ups</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Progress Section -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Overall Project Progress</h5>
                    </div>
                    <div class="card-body">
                        <div class="progress-group">
                            <span class="progress-title">Post-release Counseling</span>
                            <span class="float-right"><b>80%</b></span>
                            <div class="progress">
                                <div class="progress-bar bg-success" style="width: 80%"></div>
                            </div>
                        </div>
                        <div class="progress-group mt-3">
                            <span class="progress-title">Follow-up Cases</span>
                            <span class="float-right"><b>60%</b></span>
                            <div class="progress">
                                <div class="progress-bar bg-warning" style="width: 60%"></div>
                            </div>
                        </div>
                        <div class="progress-group mt-3">
                            <span class="progress-title">Contact with Families</span>
                            <span class="float-right"><b>90%</b></span>
                            <div class="progress">
                                <div class="progress-bar bg-primary" style="width: 90%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <!-- District-wise Data -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>District-wise Data Overview</h5>
                    </div>
                    <div class="card-body">
                        <!-- Bar Chart -->
                        <div class="chart-container">
                            <canvas id="districtBarChart"></canvas>
                        </div>
                        <!-- Summary Table -->
                        <div class="mt-4">
                            <h6>District Summary</h6>
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>District</th>
                                        <th>Total Cases</th>
                                        <th>Resolved</th>
                                        <th>Pending</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Dhaka</td>
                                        <td>120</td>
                                        <td>90</td>
                                        <td>30</td>
                                    </tr>
                                    <tr>
                                        <td>Chattogram</td>
                                        <td>100</td>
                                        <td>70</td>
                                        <td>30</td>
                                    </tr>
                                    <tr>
                                        <td>Khulna</td>
                                        <td>80</td>
                                        <td>50</td>
                                        <td>30</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PNGO-wise Data -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>PNGO-wise Contributions</h5>
                    </div>
                    <div class="card-body">
                        <!-- Doughnut Chart -->
                        <div class="chart-container">
                            <canvas id="pngoDoughnutChart"></canvas>
                        </div>
                        <!-- Details Table -->
                        <div class="mt-4">
                            <h6>PNGO Breakdown</h6>
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>PNGO</th>
                                        <th>Cases Handled</th>
                                        <th>Resolved</th>
                                        <th>Pending</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>PNGO A</td>
                                        <td>100</td>
                                        <td>80</td>
                                        <td>20</td>
                                    </tr>
                                    <tr>
                                        <td>PNGO B</td>
                                        <td>80</td>
                                        <td>60</td>
                                        <td>20</td>
                                    </tr>
                                    <tr>
                                        <td>PNGO C</td>
                                        <td>70</td>
                                        <td>50</td>
                                        <td>20</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Charts Section -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Case Distribution (Pie Chart)</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="pieChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Interventions Over Time (Bar Chart)</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="barChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Recent Activities</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Case ID</th>
                                    <th>Activity</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>CASE-101</td>
                                    <td>Filed Application</td>
                                    <td><span class="badge bg-success">Completed</span></td>
                                    <td>2024-11-24</td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>CASE-102</td>
                                    <td>Follow-up Meeting</td>
                                    <td><span class="badge bg-warning">Pending</span></td>
                                    <td>2024-11-23</td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>CASE-103</td>
                                    <td>Family Counseling</td>
                                    <td><span class="badge bg-info">Ongoing</span></td>
                                    <td>2024-11-22</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection