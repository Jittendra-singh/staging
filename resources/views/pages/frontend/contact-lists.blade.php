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
																	<th scope="col" class="text-end">Actions</th>
																</tr>
															</thead>
															<tbody>
																@if($leads->count()>0)
																@foreach ($leads as $lead)
																<tr>
																	<td><strong>{{$lead->name}}</strong></td>
																	<td><a href="mailto:{{$lead->email}}" class="text-primary">{{$lead->email}}</a></td>
																	<td><a href="tel:{{$lead->phone}}" class="text-secondary">{{$lead->phone}}</a></td>
																	<td style="max-width: 320px; word-break: break-word;">{{$lead->message}}</td>
																	<td><small class="text-muted">{{$lead->created_at->format('M d, Y h:i A')}}</small></td>
																	<td class="text-end">
																		<a href="mailto:{{$lead->email}}?subject=Re:%20Inquiry%20from%20VolunteerCleanup&body=Hi%20{{urlencode($lead->name)}},%0A%0A" class="btn btn-sm btn-outline-primary" title="Reply via Email">
																			<i class="fa-solid fa-reply me-1"></i> Reply
																		</a>
																	</td>
																</tr>
																@endforeach
																@else
																	<tr>
																		<td colspan="6" class="text-center py-4 text-muted">No contacts available yet.</td>
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