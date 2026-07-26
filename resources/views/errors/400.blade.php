@include('errors.themed', [
    'status' => 400,
    'title' => '400 Bad Request',
    'heading' => 'Bad Request',
    'message' => 'The request could not be understood or was invalid. Please check and try again.',
])
