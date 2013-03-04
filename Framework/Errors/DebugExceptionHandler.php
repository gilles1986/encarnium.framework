<?php
declare(ENCODING = 'utf-8');
namespace Framework\Errors;

/**
 * A basic but solid exception handler which catches everything which
 * falls through the other exception handlers and provides useful debugging
 * information.
 *
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 */
class DebugExceptionHandler {

	/**
	 * @var \Exception
	 */
	private $exception;
	
	/**
	 * 
	 */
	public function __construct() {
		set_exception_handler(array($this, 'handleException'));
	}

	/**
	 * 
	 * @param \Exception $exception
	 * @return	string	HTML-Code for the Exception
	 */
	public function handleException(\Exception $exception) {
		
		$this->exception = $exception;
		
		$code = '
		<html>
			<head>
				<style>
					pre {margin: 0;font-size: 11px;color: #515151;background-color: #D0D0D0;padding-left: 30px;} 
					.backtrace {border: 1px solid black; margin: 1px;}
					.file { font-size: 11px; color: green; background-color: white;}
					.header { color: rgb(105, 165, 80); background-color: rgb(65, 65, 65); padding: 4px 2px; }
					.step { color: white; }
					.tracecode  { color: rgb(105, 165, 80); }
				</style>
			</head>
			<body>' . 
			$this->createBackTraceCode($exception->getTrace()) .'
			</body>
		</html>
		';
			
		echo $code;
		
		
	}
	
	/**
	 * 
	 * @return	string
	 */
	public function createBackTraceCode(array $trace) {
		
		$backtracecode = '';
		
		$backtracecontainer = '<div class="backtrace">%s</div>';
		$stepheadercode = '<pre class="header"><span class="step">%s</span> <span class="class">%s</span></pre>';
		$mainheadercode = '<pre class="header"><span class="code">Code: %s</span> <span class="error">Message: %s</span></pre>';
			
		$current = sprintf($mainheadercode, $this->exception->getCode(), $this->exception->getMessage());
		$current .= $this->getCodeSnippet($this->exception->getFile(), $this->exception->getLine());
		$backtracecode .= sprintf($backtracecontainer, $current);
		
		if(count($trace)) {
			//Verschiedene Trace stufen durchloopen
			foreach ($trace as $index => $step) {
				
				($step['class']) ? $class =  $step['class'] .'::' . $step['function'] : $class = $step['function']; 

				$class .= '(';
				
				$arguments = '';
				
				//Übergebende Argumente ausgeben
				foreach ($step['args'] as $argument) {
					$arguments .= ((strlen($arguments)) === 0) ? '' : ', ';
					if(is_object($argument)){
						$arguments .= get_class($argument);
					}
					elseif(is_string($argument)) {
						$arguments .= $argument;
					}
					elseif(is_numeric($argument)){
						$arguments .= (string)$argument;
					}
					else {
						$arguments .= gettype($argument);
					}								
				}
				$class .= $arguments;
				$class .= ')';
				
				$stepcode = sprintf($stepheadercode, count($trace) - $index, $class);
				$stepcode .= $this->getCodeSnippet($step['file'], $step['line']);
				$backtracecode .= sprintf($backtracecontainer, $stepcode);
				
			}			
		}	
		
		return $backtracecode;
		
	}
	
	/**
	 * Returns a code snippet from the specified file.
	 *
	 * @param string $filePathAndName Absolute path and file name of the PHP file
	 * @param integer $lineNumber Line number defining the center of the code snippet
	 * @return string The code snippet
	 * @author Robert Lemke <robert@typo3.org>
	 */
	protected function getCodeSnippet($filePathAndName, $lineNumber) {
		$pathPosition = strpos($filePathAndName, 'Packages/');
		if (@file_exists($filePathAndName)) {
			$phpFile = @file($filePathAndName);
			if (is_array($phpFile)) {
				$startLine = ($lineNumber > 2) ? ($lineNumber - 2) : 1;
				$endLine = ($lineNumber < (count($phpFile) - 2)) ? ($lineNumber + 3) : count($phpFile) + 1;
				if ($endLine > $startLine) {
					if ($pathPosition !== FALSE) {
						$codeSnippet = '<span class="file">' . substr($filePathAndName, $pathPosition) . ':</span><br /><pre>';
					} else {
						$codeSnippet = '<span class="file">' . $filePathAndName . ':</span><br /><pre>';
					}
					for ($line = $startLine; $line < $endLine; $line++) {
						$codeLine = str_replace("\t", ' ', $phpFile[$line-1]);

						if ($line === $lineNumber) {
							$codeSnippet .= '</pre><pre class="tracecode">';
						}
						$codeSnippet .= sprintf('%05d', $line) . ': ' . $codeLine;
						if ($line === $lineNumber) {
							$codeSnippet .= '</pre><pre>';
						}
					}
					$codeSnippet .= '</pre>';
				}
			}
		}
		return $codeSnippet;
	}
	
}

?>