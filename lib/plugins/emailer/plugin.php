<?php
/**
 * Emailer
 *
 * @category   Plugin.PHPMailer.Emailer
 * @package    plugin.php
 * @site       www.biggleswadesc.org
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones 
 */

/*
 *
 */
require_once dirname(__FILE__). '/PHPMailerAutoload.php';
class Emailer extends PHPMailer
{
	public	$Host			= 'smtp.gmail.com';
	public	$Port			= 587;
	public	$SMTPSecure		= 'tls';
	public	$SMTPAuth		= true;
	public	$Username		= 'biggleswadesc@gmail.com';
	public	$Password		= 'BWSC-Biggleswade';
	public	$SMTPKeepAlive	= true;
	
	/**
     * Constructor
     * @param boolean $exceptions Should we throw external exceptions?
     */
    public function __construct($exceptions = false)
    {
        $this->exceptions = ($exceptions == true);
        //Make sure our autoloader is loaded
        if (version_compare(PHP_VERSION, '5.1.2', '>=')) {
            $autoload = spl_autoload_functions();
            if ($autoload === false or !in_array('PHPMailerAutoload', $autoload)) {
                require 'PHPMailerAutoload.php';
            }
        }
		$this->isSMTP();;
		$this->setFrom('no-reply@biggleswadesc.org', 'Biggleswade Swimming Club');
		$this->addReplyTo('admin@biggleswadesc.org', 'Biggleswade Swimming Club');
		$this->AddEmbeddedImage(dirname(__FILE__).'/bwsclogo.png', 'bwsclogo');
    }
	
	/**
     * Create a message from an HTML string.
     * Automatically makes modifications for inline images and backgrounds
     * and creates a plain-text version by converting the HTML.
     * Overwrites any existing values in $this->Body and $this->AltBody
     * @access public
     * @param string $message HTML message string
     * @param string $basedir baseline directory for path
     * @param boolean $advanced Whether to use the advanced HTML to text converter
     * @return string $message
     */
    public function msgHTML($message, $basedir = '', $advanced = false)
    {
        $message = file_get_contents(dirname(__FILE__).'/email_template_a.html').$message.file_get_contents(dirname(__FILE__).'/email_template_b.html');
        preg_match_all('/(src|background)=["\'](.*)["\']/Ui', $message, $images);
        if (isset($images[2])) {
            foreach ($images[2] as $imgindex => $url) {
                // do not change urls for absolute images (thanks to corvuscorax)
                if (!preg_match('#^[A-z]+://#', $url)) {
                    $filename = basename($url);
                    $directory = dirname($url);
                    if ($directory == '.') {
                        $directory = '';
                    }
                    $cid = md5($url) . '@phpmailer.0'; // RFC2392 S 2
                    if (strlen($basedir) > 1 && substr($basedir, -1) != '/') {
                        $basedir .= '/';
                    }
                    if (strlen($directory) > 1 && substr($directory, -1) != '/') {
                        $directory .= '/';
                    }
                    if ($this->addEmbeddedImage(
                        $basedir . $directory . $filename,
                        $cid,
                        $filename,
                        'base64',
                        self::_mime_types(self::mb_pathinfo($filename, PATHINFO_EXTENSION))
                    )
                    ) {
                        $message = preg_replace(
                            '/' . $images[1][$imgindex] . '=["\']' . preg_quote($url, '/') . '["\']/Ui',
                            $images[1][$imgindex] . '="cid:' . $cid . '"',
                            $message
                        );
                    }
                }
            }
        }
        $this->isHTML(true);
        // Convert all message body line breaks to CRLF, makes quoted-printable encoding work much better
        $this->Body = $this->normalizeBreaks($message);
        $this->AltBody = $this->normalizeBreaks($this->html2text($message, $advanced));
        if (empty($this->AltBody)) {
            $this->AltBody = 'To view this email message, open it in a program that understands HTML!' .
                self::CRLF . self::CRLF;
        }
        return $this->Body;
    }
}
?>