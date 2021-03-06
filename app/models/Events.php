<?php

class Events extends BaseModel {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */

	protected $guarded = array('id');  // Important

	protected $table = 'events';

	public function category()
	{
		return $this->belongsTo('EventCategory', 'event_category_id');
	}

	public function user()
	{
		return $this->belongsTo('User');
	}

	public function city()
	{
		return $this->belongsTo('City');
	}

	public function eventcategory()
	{
		return $this->belongsTo('EventCategory','event_category_id','id');
	}


	public function defaultPhoto()
	{
		return $this->belongsTo('Photo', 'default_photo_id');
	}

	public function coverPhoto()
	{
		return $this->belongsTo('Photo', 'cover_photo_id');
	}


	public static function getById($input){
		
		if(Events::checkSlugName($input) == 1){

			return Events::where('slug',$input)->first();

		}
		else{
			
			return false;

		}

	}

	public static function checkSlugName($input){

		return Events::where('slug',$input)->count();
	
	}

	public static function updateUserId(){
		try {
	    		$update = Events::find(Session::get('update_id'));
				$update->fill(array(
					'user_id' => Auth::user()->id
				));
				$update->save();
	    		return "ok";
	   
	    } catch (Exception $e) {
	    		return "no";
	    }
	}


	public static function StoreEvent($input) {

		$started_at = empty($input['started_at']) ? 'no' :  preg_split("/([\/: ])/", $input['started_at']);
		$ended_at = empty($input['ended_at']) ? 'no' : preg_split("/([\/: ])/", $input['ended_at']);

		$input =  array(
			'event_category_id'=> $input['event_category_id'],
			'city_id'=> $input['city_id'],
			'email'=> $input['email'],
			'name'=> $input['name'],
			'user_id'=> Auth::check() ? Auth::user()->id : '',
			'stewardship' => $input['stewardship'],
			'description' => $input['description'],
			'location' => $input['location'],
			'website_url' => $input['website_url'],
			'social_media_urls' => $input['social_media_urls'],
			'started_at' => time(),
			'ended_at' => time(),
			// 'started_at' => $started_at == 'no' ? '' : 
			// mktime((int) $started_at[3], (int) $started_at[4],0,(int) $started_at[0],(int) $started_at[1],(int) $started_at[2]),
			// 'ended_at' => $ended_at == 'no' ? '' : 
			// mktime((int) $ended_at[3], (int) $ended_at[4],0,(int) $ended_at[0],(int) $ended_at[1],(int) $ended_at[2]),
		 );


		$rules =  array(
			'event_category_id'=> 'required',
			'city_id'=> 'required',
			// 'email'=> 'required|email',
			'name'=> 'required',
			'stewardship' => 'required|min:5',
			'description' => 'required|min:5',
			'location' => 'required',
			// 'website_url' => 'required|url',
			// 'social_media_urls' => 'required',
			'started_at' => 'required',
			'ended_at' => 'required',
		 );

		$validator = Validator::make($input, $rules);

  	  	if ($validator->fails()) {
  	 		return $validator->errors()->all();
	    } 
	    else {
	    	try {

	    		$event = new Events;
	    		$event->fill($input);
	    		$event->save();

				$photo = Photo::saveAvatar('events', $event->id);

	   			// update 
	    		$update = Events::find($event->id);
				$update->fill(array(
				    'slug' => Events::checkSlugName(Str::slug($input['name'])) > 0 ? 
				    strtolower(Str::slug($input['name'])).$event->id : 
				    strtolower(Str::slug($input['name'])),
				    'default_photo_id' => $photo['default_photo_id'],
				    'cover_photo_id' => $photo['cover_photo_id'],
				));
				$update->save();
	    		return "ok";
	   
	    	} catch (Exception $e) {
	    		return "no";
	    	}
	    }

	}

	public static function UpdateEvent($input) {

		$started_at = empty($input['started_at']) ? 'no' :  preg_split("/([\/: ])/", $input['started_at']);
		$ended_at = empty($input['ended_at']) ? 'no' : preg_split("/([\/: ])/", $input['ended_at']);

	    $id = $input['id'];
		
		$input =  array(
			'event_category_id'=> $input['event_category_id'],
			'city_id'=> $input['city_id'],
			'email'=> $input['email'],
			'name'=> $input['name'],
			'user_id'=> Auth::check() ? Auth::user()->id : '',
			'stewardship' => $input['stewardship'],
			'description' => $input['description'],
			'location' => $input['location'],
			'website_url' => $input['website_url'],
			'social_media_urls' => $input['social_media_urls'],
			'started_at' => time(),
			'ended_at' => time(),
			// 'started_at' => $started_at == 'no' ? '' : 
			// mktime((int) $started_at[3], (int) $started_at[4],0,(int) $started_at[0],(int) $started_at[1],(int) $started_at[2]),
			// 'ended_at' => $ended_at == 'no' ? '' : 
			// mktime((int) $ended_at[3], (int) $ended_at[4],0,(int) $ended_at[0],(int) $ended_at[1],(int) $ended_at[2]),
		 );


		$rules =  array(
			'event_category_id'=> 'required',
			'city_id'=> 'required',
			// 'email'=> 'required|email',
			'name'=> 'required',
			'stewardship' => 'required|min:5',
			'description' => 'required|min:5',
			'location' => 'required',
			// 'website_url' => 'required|url',
			// 'social_media_urls' => 'required',
			'started_at' => 'required',
			'ended_at' => 'required',
		 );

		$validator = Validator::make($input, $rules);

  	  	if ($validator->fails()) {
  	 		return $validator->errors()->all();
	    } 
	    else {


	    		$event = Events::find($id);
	    		$event->fill($input);
	    		$event->save();
				$photo = Photo::updateAvatar($event->id,'events');
	   			// update 
	    		$update = Events::find($event->id);
				$update->fill(array(
				    'slug' => Events::checkSlugName(Str::slug($input['name'])) > 0 ? 
				    strtolower(Str::slug($input['name'])).$event->id : 
				    strtolower(Str::slug($input['name'])),
				));
				$update->save();
	    		return "ok";	   
	    	
	    }

	}
	public static function createEvent($input) {
		$started_at = empty($input['started_at']) ? 'no' :  preg_split("/([\/: ])/", $input['started_at']);
		$ended_at = empty($input['ended_at']) ? 'no' : preg_split("/([\/: ])/", $input['ended_at']);

		$input =  array(
			'event_category_id'=> $input['event_category_id'],
			'city_id'=> $input['city_id'],
			'email'=> $input['email'],
			'name'=> $input['name'],
			'user_id'=> Auth::check() ? Auth::user()->id : '',
			'stewardship' => $input['stewardship'],
			'description' => $input['description'],
			'location' => $input['location'],
			'website_url' => $input['website_url'],
			'social_media_urls' => $input['social_media_urls'],
			'started_at' => $started_at == 'no' ? '' : 
			mktime((int) $started_at[3], (int) $started_at[4],0,(int) $started_at[0],(int) $started_at[1],(int) $started_at[2]),
			'ended_at' => $ended_at == 'no' ? '' : 
			mktime((int) $ended_at[3], (int) $ended_at[4],0,(int) $ended_at[0],(int) $ended_at[1],(int) $ended_at[2]),
		 );


		$rules =  array(
			'event_category_id'=> 'required',
			'city_id'=> 'required',
			// 'email'=> 'required|email',
			'name'=> 'required',
			'stewardship' => 'required|min:5',
			'description' => 'required|min:5',
			'location' => 'required',
			// 'website_url' => 'required|url',
			// 'social_media_urls' => 'required',
			'started_at' => 'required',
			'ended_at' => 'required',
		 );

		$validator = Validator::make($input, $rules);

  	  	if ($validator->fails()) {
  	 		return $validator->errors()->all();
	    } 
	    else {
	    	try {

	    		$event = new Events;
	    		$event->fill($input);
	    		$event->save();

	    		// digunakan untuk mengambil id user yang belum login
				if(!Auth::check()) {
					Session::put('update_id',$event->id);
				}
	    		// update 
	    		$update = Events::find($event->id);
				$update->fill(array(
				    'slug' => Events::checkSlugName(Str::slug($input['name'])) > 0 ? 
				    strtolower(Str::slug($input['name'])).$event->id : 
				    strtolower(Str::slug($input['name'])),
				));
				$update->save();
	    		return "ok";
	   
	    	} catch (Exception $e) {
	    		return "no";
	    	}
	    }
	}
}