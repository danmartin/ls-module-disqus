<?

	class Disqus_Actions extends Cms_ActionScope {
		public function items() {
			$this->data['items'] = Portfolio_Item::create()->where('is_enabled=1')->find_all();
		}
	}
	