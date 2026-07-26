@include('errors.themed', [
    'status' => 500,
    'title' => '500 Error',
    'heading' => 'Internal Server Error',
    'message' => "We're sorry, but something went wrong on our end. Please try again later or contact support if the problem persists.",
])
