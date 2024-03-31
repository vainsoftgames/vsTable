<?php
	class vsTable {
		public $results = [];
		public $debug = false;
		private $headers = [];
		public $rows = 0;

		public function __construct($data, $debug=false){
			$this->debug = $debug;
			$this->results['table'] = '<table class="vsTable" cellspacing="0" cellpadding="0">';
			$this->results['tfooter'] = '';
			$this->rows = (isset($data['rows']) ? count($data['rows']) : 0);

			$this->buildTHEAD($data);
			$this->buildTBODY($data);
		}

		private function buildTHEAD($data){
			$this->results['thead'] = ($this->debug ? "\n\t" : '') .'<thead>';
			$this->results['thead'] .= ($this->debug ? "\n\t\t" : '') .'<tr>';

			if(isset($data['cols'])){
				foreach($data['cols'] as $colIndex=>$col){
					$this->headers[] = $col['type'];
					$this->results['thead'] .= ('<td class="'. $col['type'] . (isset($col['sort']) ? ' sortable' : '') . (isset($col['optional']) ? ' optional' : '') .'"'. (isset($col['sort']) ? ' onClick="ajaxRequest_tableSort(this, \''. $col['sort'] .'\');"' : '') . (isset($col['alt']) ? ' title="'. $col['alt'] .'"': '') .'>');
						$this->results['thead'] .= ($col['label'] != '' ? $col['label'] : '&nbsp;');
					$this->results['thead'] .= '</td>';
				}
			}

			$this->results['thead'] .= ($this->debug ? "\n\t\t" : '') .'</tr>';
			$this->results['thead'] .= ($this->debug ? "\n\t" : '') .'</thead>';
		}
		public function buildTFOOTER($pageNum, $pageTotal, $pageLimit=20, $selector=false){
			if(($pageNum == $pageTotal && $pageTotal <= 1) || $pageTotal < 1) return;

			$this->results['tfooter'] = '<tfooter>';
				$this->results['tfooter'] .= '<tr>';
					$this->results['tfooter'] .= '<td class="pageNav_td" colspan="'. count($this->headers) .'">';
						$this->results['tfooter'] .= '<div class="pageNav">';
							$this->results['tfooter'] .= '<div class="item'. ($pageNum <= 1 ? ' inactive' : '" onClick="vsQuery_pageNav(this, \'first\');') .'" title="First Page"></div>';

							$this->results['tfooter'] .= '<div class="item'. ($pageNum <= 1 ? ' inactive' : '" onClick="vsQuery_pageNav(this, \'prev\');') .'"><span><</span> Prev '. $pageLimit .' Results</div>';
							$this->results['tfooter'] .= '<div class="item">';
								$this->results['tfooter'] .= '<div class="pageOf" onClick="vsQuery_pageNav($(this).parent(), \'all\');" title="View All">'. $pageNum .' of '. $pageTotal .'</div>';
								$this->results['tfooter'] .= '<div class="pageNav_bottom"onClick="vsQuery_pageNav($(this).parent(), \'prompt\')" title="Jump to Page"></div>';
							$this->results['tfooter'] .= '</div>';
							$this->results['tfooter'] .= '<div class="item'. ($pageNum == $pageTotal ? ' inactive' : '" onClick="vsQuery_pageNav(this, \'next\');') .'">Next '. $pageLimit .' Results <span>></span></div>';

							$this->results['tfooter'] .= '<div class="item'. ($pageNum == $pageTotal ? ' inactive' : '" onClick="vsQuery_pageNav(this, \'last\');') .'" title="Last Page"></div>';
						$this->results['tfooter'] .= '</div>';
					$this->results['tfooter'] .= '</td>';
				$this->results['tfooter'] .= '</tr>';
			$this->results['tfooter'] .= '</tfooter>';
		}

		private function buildTBODY($data){
			if(!isset($data['rows']) || count($data['rows']) == 0){
				$this->results['tbody'] = '';
				return;
			}

			$this->results['tbody'] = '<tbody>';

			foreach($data['rows'] as $rowIndex=>$row){
				$this->results['tbody'] .= '<tr'. (isset($row['id']) ? (' data-rowID="'. $row['id'] .'"') : '') .'>';
					foreach($row['c'] as $colIndex=>$col){
						if(count($this->headers) >= ($colIndex+1) && $this->headers[$colIndex] == 'boolean') $v = ($col['v'] == 1 ? '&#x2713;' : '&#x2717;');
						else $v = (isset($col['f']) && $col['f'] != '' ? $col['f'] : '&nbsp;');

						$classes = [];
						// Pull header classes
						if(count($this->headers) >= $colIndex+1){
							$classes[] = $this->headers[$colIndex];
						}
						// Check if column is optional
						if(isset($col['optional'])) $classes[] = 'optional';
						else if(count($data['cols']) >= $colIndex+1 && isset($data['cols'][$colIndex]['optional']))  $classes[] = 'optional';
						// See if custom bgColor class is defined (usually only for Projects)
						if(isset($col['bgColor'])) $classes[] = $col['bgColor'];


						$styles = [];
						if(isset($col['onClick'])) $styles[] = 'cursor:pointer';

						if(count($this->headers) <= $colIndex){
							if(isset($col['style'])) $styles[] = $col['style'];
						}
						else if($this->headers[$colIndex] == 'color') $styles[] = 'width:1px;background-color:'. $v;

						if(isset($col['style'])) $styles[] = $col['style'];

						$this->results['tbody'] .= '<td';
							if(count($classes) > 0)		$this->results['tbody'] .= ' class="'. implode(' ', $classes) .'"';
							if(count($styles) > 0)		$this->results['tbody'] .= ' style="'. implode(';', $styles) .'"';
							if(isset($col['onClick']))	$this->results['tbody'] .= ' onClick="'. $col['onClick'] .'"';
							if(isset($col['alt']))		$this->results['tbody'] .= ' title="'. $col['alt'] .'"';
							if(isset($col['altF']))		$this->results['tbody'] .= ' onMouseOver="$(this).html(\''. $col['altF'] .'\');" onMouseOut="$(this).html(\''. $v .'\');"';
							if(isset($col['colspan'])){
								if($col['colspan'] == 'count') $this->results['tbody'] .= ' colspan="'. count($this->headers) .'"';
								else $this->results['tbody'] .= ' colspan="'. $col['colspan'] .'"';
							}
						$this->results['tbody'] .= '>';

							if(count($this->headers) <= $colIndex || $this->headers[$colIndex] != 'color') $this->results['tbody'] .= $v;

						$this->results['tbody'] .= '</td>';
					}
				$this->results['tbody'] .= '</tr>';
			}

			$this->results['tbody'] .= '</tbody>';
		}

		public function buildTable(){
			return $this->results['table'].
					$this->results['thead'].
					$this->results['tbody'].
					$this->results['tfooter'].
					'</table>';
		}
	}
	
	
	// vsTable Sort Status Indicator
	function create_sortStatus($sortFilter){
		global $render;
		if($render != 'php') return '';

		global $sort, $sortDIR;

		if($sortFilter == $sort){
			if($sortDIR != 'DESC') return '<span class="sort_arrow active">&#x25B2;</span>';
			else return '<span class="sort_arrow active">&#x25BC;</span>';
		}
		else return '<span class="sort_arrow">&#x25BC;</span>';
	}
?>
