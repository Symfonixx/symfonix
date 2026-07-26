@include('errors.themed', [
    'status' => 404,
    'title' => '404 Error',
    'heading' => 'Oops! Page Not Found!',
    'message' => 'The page you are looking for does not exist. It might have been moved or deleted.',
    'showImage' => true,
])
