@extends('layouts.frontend')
@section('content')
    <div class="wrapper wrapper-body">
		<div class="dashboard-body">
			<div class="container-fluid">
				<div class="row">
					<div class="col-md-12 d-flex align-items-center justify-content-between">
						<div class="d-main-title">
							<h3><i class="fa-solid fa-calendar-days me-3 mb-3"></i>Inbox</h3>
							@php						
								$breadcrumbs = [
										[
											'name' => 'Inbox', 
											'url' => route('contact-lists')
										],
									]
							@endphp     
							<x-reusables.frontend.breadcrumb :breadcrumbs="$breadcrumbs"/>
						</div>
                        
					</div>
                    
					<div class="col-md-12">
						<div class="conversion-setup">
							<div class="main-card mt-5">
								<div class="dashboard-wrap-content p-4">
									<div>
										<div>
											<div class="table-card mt-4">
												<div class="main-table">
													<div class="table-responsive">
														<table class="table">
															<thead class="thead-dark">
																<tr>
																	<th scope="col">Name</th>
																	<th scope="col">Email</th>
																	<th scope="col">Phone</th>
																	<th scope="col">Message</th>
																	<th scope="col">Date Created</th>
                                                                    <th></th>
																</tr>
															</thead>
															<tbody>
                                                    			@if($leads->count()>0)
																@foreach ($leads as $lead)
																<tr>										
																	<td>{{$lead->name}}</td>	
																	<td>{{$lead->email}}</td>	
																	<td>{{$lead->phone}}</td>	
																	<td>{{$lead->message}}</td>
																	<td>{{$lead->created_at->format('M d, Y h:i A')}}</td>
																</tr>
																@endforeach
																@else
																	<tr>
																		<td colspan="5" class="text-center">No contact available</td>
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
	
@endsection