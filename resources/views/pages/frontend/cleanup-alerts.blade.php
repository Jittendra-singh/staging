@extends('layouts.frontend')
@section('title')
    Cleanup Alerts
@endsection
@section('content')
    	<div class="wrapper wrapper-body">
		<div class="dashboard-body">
			<div class="container-fluid">
				<div class="row">
					<div class="col-md-12">
						<div class="d-main-title">
							<h3><i class="fa-solid fa-bell me-3 mb-3"></i>Weekly Email Alerts</h3>
							@php
								$breadcrumbs = [
									[
										'name' => 'Weekly Email Alerts', 
										'url' => route('front-email-alerts')
									],
								]
							@endphp     
							<x-reusables.frontend.breadcrumb :breadcrumbs="$breadcrumbs"/>
                            
						</div>
					</div>
					<div class="col-md-12">
						<div class="conversion-setup">
							<div class="main-card mt-5">
								<!-- <div class="dashboard-wrap-content p-4">
									<div class="d-md-flex flex-wrap align-items-center justify-content-center">
										<div class="nav custom2-tabs btn-group">
											<h5>Coming Soon</h5>
										</div>										
									</div>								
								</div> -->
								<div class="dashboard-wrap-content p-4">
									<div class="d-md-flex flex-wrap align-items-center justify-content-end">
										<div class="nav custom2-tabs btn-group">
											<button class="primary-btn w-fits" data-bs-toggle="modal" data-bs-target="#manageAlerts">Add Alert</button>
										</div>										
									</div>
									<div>
										<div>
											<div class="table-card mt-4">
												<div class="main-table">
													<div class="table-responsive">
														<table class="table">
															<thead class="thead-dark">
																<tr>
																	<th scope="col">Alert Name</th>
																	<th scope="col">Alert Zipcode</th>
																	<th scope="col">Created From</th>
																	<th scope="col">Date Created</th>
																	<th scope="col">Status</th>
                                                                    <th></th>
																</tr>
															</thead>
															<tbody>
																@foreach ($listing_data as $data)
																	
																<tr class="single-tr">										
																	<td>{{$data->alert_name}}</td>	
																	<td>{{$data->zipcode}}</td>
																	@if($data->created_from==1)	
																		<td>Alert Page</td>
																	@elseif($data->created_from==2)
																		<td>Registration Page</td>
																	@elseif($data->created_from==3)
																		<td>Booking Page</td>
																	@endif
																	<td>{{$data->created_at}}</td>
																	<!-- <td>
																		@if($data->status)
																			<span class="text-success">Enable</span>
																		@else
																			<span class="text-danger">Disabled</span>
																		@endif
																	</td> -->
																	<td>
																		@if($data->status)
																			<span class="text-success">Enable</span>
																		@else
																			<span class="text-danger">Disabled</span>

																			@if($data->status_updated_by_role == 2)
																				<br>
																				<small class="text-muted">
																					By
																					<span data-bs-toggle="tooltip" data-bs-original-title="Disabled at {{ $data->updated_at ? date(config('app.dateTimeFormat'), strtotime($data->updated_at)) : 'N/a' }}">
																						{{ @$data->statusUpdatedByAdmin->name ?: 'Admin' }}
																					</span>
																				</small>
																			@endif
																		@endif
																	</td>

                                                                    <td>
                                                                        <div class="dropdown position-absolute translate-middle" >
                                                                            <button class="option-btn" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa-solid fa-ellipsis-vertical"></i></button>
                                                                            <div class="dropdown-menu dropdown-menu-right">
                                                                                <a class="dropdown-item" href="{{route('view-events', ['sort_by_date' => 1,'location' => $data->zipcode,'latitude' => $data->location_lat,'longitude' => $data->location_long,'distance' => '15','unit' =>'mi'])}}">
																					<i class="fa-solid fa-eye"></i> View Event
																				</a>
																				@if(false)
                                                                                <a class="dropdown-item" href="{{route('front-trigger-email-alerts', ['id' => $data->id])}}">
																					<i class="fa-solid fa-envelope"></i> Trigger Email
																				</a>
																				@endif
																				@if(!($data->status == 0 && $data->status_updated_by_role == 2))
                                                                                <a class="dropdown-item" href="{{route('front-disable-email-alerts', ['id' => $data->id])}}">
																					<i class="fa-solid fa-ban"></i> {{$data->status ? 'Disable trigger' : 'Enable trigger'}}
																				</a>
																				@endif
                                                                                <a class="dropdown-item delete-record" data-url="{{route('front-delete-alerts', ['id' => $data->id])}}">
																					<i class="fa-solid fa-trash-can"></i> Delete
																				</a>
                                                                            </div>
                                                                        </div>
                                                                    </td>
																</tr>
																@endforeach
																@if($listing_data->isEmpty())
																<tr>
																	<td colspan="6" class="text-center">No alert found.</td>
																</tr>
																@endif
															</tbody>									
														</table>
													</div>
												</div>
											</div>
										</div>										
									</div>
								</div>
							</div>							
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
    <x-reusables.frontend.manage-alert />
@endsection