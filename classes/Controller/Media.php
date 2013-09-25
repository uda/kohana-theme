<?php defined('SYSPATH') OR die('No direct script access.');

class Controller_Media extends Controller
{
  public function action_index()
  {
    // Get the file path from the request
    $file = $this->request->param('file');

    // Find the file extension
    $ext = pathinfo($file, PATHINFO_EXTENSION);

    // Remove the extension from the filename
    $file = substr($file, 0, -(strlen($ext) + 1));

    // Find the file in 'media/' dirs across the cascading file system
    $filename = Kohana::find_file('', $file, $ext);

    if ($filename) {
      // Get the file content and deliver it
      $this->response->body(file_get_contents($filename));

      // Set the proper headers to allow caching
      $this->response->headers('Content-Type', File::mime_by_ext($ext));
      $this->response->headers('Content-Length', filesize($filename));
      $this->response->headers('Last-Modified', date('r', filemtime($filename)));
    } else {
      $this->response->status(404);
      // Throw some suitable exception here
    }
  }
}
