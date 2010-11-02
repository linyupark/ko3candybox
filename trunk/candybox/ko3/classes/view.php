<?php defined('SYSPATH') or die('No direct script access.');

class View extends Kohana_View {
    
    /**
     * Sets the view filename.
     *
     *     $view->set_filename($file);
     *
     * @param   string  view filename
     * @return  View
     * @throws  Kohana_View_Exception
     */
    public function set_filename($file)
    {
            if (($path = Kohana::find_file('views', $file, VEXT)) === FALSE)
            {
                    throw new Kohana_View_Exception('The requested view :file could not be found', array(
                            ':file' => $file,
                    ));
            }

            // Store the file path locally
            $this->_file = $path;

            return $this;
    }
}
