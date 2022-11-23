<?php
	include 'global.functions.php';
	
	if($_SERVER['REQUEST_METHOD'] == 'POST'){
		if(isset($_POST['search_term'])){
			$search_term = trim($_POST['search_term']);
			
			if(!empty($search_term)){
				// Autorization
				$access_token = autorize();
				
				if($access_token !== false){
					// Get Data
					$result = get_data($access_token);
					
					if($result){
						// Search
						$filtered_data = search($search_term, $result);
					}
				}
			}
			else{
				$filtered_data = false;
			}
		}
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Index</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.1/dist/jquery.min.js" integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
	<script>
		var search_term = '<?php echo isset($search_term) ? $search_term : ''; ?>',
			timer = null,
			progress = 100;
		
		$(document).ready(function(){
			initialize_timer();
			
			$('#uploadModal').on('hidden.bs.modal', function(e){
				$('#file_select_form')[0].reset();
				
				$('#image_preview').attr('src', '').width(0).height(0);
			});
			
			$('#formFile').on('change', function(){
				read_content(this);
			});
		});
		
		function initialize_timer(){
			if(timer === null){
				timer = window.setInterval(function(){
					decrease_time();
				}, 3600000);
			}
		}
		
		function decrease_time(){
			if(progress > 0){
				progress -= 1.66;
			}
			else{
				clearInterval(timer);
				
				timer = null;
				
				send_search();
			}
			
			if(progress < 0){
				progress = 0;
			}
			
			$('.progress-bar').css('width', progress + '%').attr('aria-valuenow', progress);
		}
		
		function send_search(){
			$.ajax({
				url: 'ajax.php',
				method: 'POST',
				dataType: 'json',
				data: {
					term: search_term
				},
				beforeSend: function(){
					progress = 100;
					$('.progress-bar').css('width', progress + '%').attr('aria-valuenow', progress);
				},
				success: function(data){
					if(data !== undefined){
						var html = '';
						
						$.each(data, function(key, val){
							html += '<tr>';
							html += '<th scope="row" class="text-nowrap">' + val.task + '</th>';
							html += '<td class="text-nowrap">' + val.title + '</td>';
							html += '<td>' + val.description + '</td>';
							html += '<td style="background-color: ' + val.colorCode + '">&nbsp;</td>';
							html += '</tr>';
						});
						
						$('#result_table tbody').empty().html(html);
						
						html = '';
					}
					
					initialize_timer();
				},
				error: function(){
				}
			});
		}
		
		function read_content(input){
			if (input.files && input.files[0]) {
				var reader = new FileReader();
				
				reader.onload = function(e) {
					$('#image_preview').attr('src', e.target.result).width(150).height(200);
				};
				
				reader.readAsDataURL(input.files[0]);
			}
		}
	</script>
</head>
<body>
	<div class="container py-5">
		<form method="POST" action="search.php">
			<div class="d-flex justify-content-center align-items-start">
				<input type="text" name="search_term" class="form-control w-50 me-3" value="<?php echo isset($search_term) ? $search_term : ''; ?>" id="search_term" autocomplete="off">
				<button type="submit" class="btn btn-primary mb-3 me-3">Search</button>
				<a href="index.php" class="btn btn-secondary mb-3">Show All</a>
			</div>
		</form>
		<div class="d-flex justify-content-center">
			<div class="progress w-75">
				<div class="progress-bar" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
			</div>
		</div>
		<table class="table" id="result_table">
			<thead>
				<tr>
					<th scope="col">task</th>
					<th scope="col">title</th>
					<th scope="col">description</th>
					<th scope="col" width="1%">colorCode</th>
				</tr>
			</thead>
			<tbody>
				<?php
				if(isset($filtered_data) && !empty($filtered_data)){
					foreach($filtered_data as $key => $val){
				?>
				<tr>
					<th scope="row" class="text-nowrap"><?php echo $val->task; ?></th>
					<td class="text-nowrap"><?php echo $val->title; ?></td>
					<td><?php echo $val->description; ?></td>
					<td style="background-color: <?php echo $val->colorCode; ?>">&nbsp;</td>
				</tr>
				<?php
					}
				}
				?>
			</tbody>
		</table>
		<div class="d-flex justify-content-center">
			<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
				Show Modal
			</button>
		</div>
	</div>
	
	<!-- Modal -->
	<div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h1 class="modal-title fs-5" id="uploadModalLabel">Modal title</h1>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<div class="mb-3">
						<form id="file_select_form">
							<label for="formFile" class="form-label">Select any image</label>
							<input class="form-control" type="file" id="formFile" accept="image/gif, image/jpeg, image/png">
							<img id="image_preview" />
						</form>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
</body>
</html>