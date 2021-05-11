<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>WP Rest API</title>
    <script src="http://code.jquery.com/jquery-1.11.3.min.js"></script>
</head>
<body>
 
<h3>Fetch Posts from WordPress</h3>
{{dd($categories)}}
@php $i=0; @endphp
@foreach ($posts as $post)
	@php $i++; @endphp
	<h2>{{$post->title->rendered}}--{{$post->date}}</h2>
@endforeach
<p>Total Count :- {{$i}}</p>
</body>
</html>