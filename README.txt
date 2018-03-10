The Neighborhood Organizing System ("NOS") is a unique software program created by Matt Swindle (mattthew_swindle@yahoo.com) and Mica Cardillo (mica.cardillo@gmail.com) exclusively for The Neighborhood Food Project.  Development is ongoing.
  

THere are sections of code which are used in this project under a variety of pass-through open source licensing models, along with code that is being re-used from previous projects.  Except where we recycled work from pre-existing projects, the work output is exclusively liscensed to The Neighborhood Food Project.  You must obtain express written consent from The Neighborhood Food Project to re-use any aspect of this project. 

Neighborhood Organizing System
Copywrite 2012-2013 The Neighborhood Food Project.

********************************************************************************	
BUGS I NOTICED

	Full region should have one regionAtLarge neighborhood
	When aNewDistrict created, also created is aNewDistrictAtLarge neighborhood
	
	all pages should, when reloaded, return to the same tool as when 'save' (or whatever) was clicked.
		- if I save zoom level in neighborhood mapTools, the reload should return me to mapTools
		- if I accept donor, reload should return me to acceptDonor tool
		- etc.
		
	*	Welcome committee memberdata form needs nhood dropdown instead of textbox

	tallysheet assumes only one neighborhood is assigned to each NC
	
	route polyline doesn't save
	
	neighborhood.php "save notes" needs to be parameterized
	
	district.php "save notes" needs to be parameterized
	
	*	wholeProject.php "save notes" needs to be parameterized

********************************************************************************
FUTURE FEATURES

	MAP
		- markers should be draggable
		- polygons for districts and neighborhoods
		
	APP
		
	
********************************************************************************
ROLE REQUIREMENTS:

Neighborhood Coordinator (NC)

*    Need to be able to enter new Food Donors.  When they enter the New Food Donor's info, that person is automatically assigned to his/her Neighborhood.
*    Be able to view a table view of his/her current Food Donors.
*    Be able to edit the Food Donor's information.
*    Be able to record notes for specific Food Donors (not surfaced to the Food Donor).
*    Be able to record general notes about Neighborhood (not surfaced to the Food Donor) for future NCs.
*    Be able to assign a pickup order to his/her list of Food Donors.
*    Click on a link that allows for a printer-friendly version of Tallysheet.  The Tallysheet should always print-out in the pickup order.   This will help the Data Manager enter the FD history more efficiently.
    Be able to print out maps of his/her Food Donors.

District Coordinator (DC)
*    Access a summary of the Neighborhood Coordinators assigned to him/her and their contact information.  Does not necessarily need to be able to reassign his/her NCs or FDs, but would be nice if we could make this possible without having to give them access to other districts’ data.
*    Be able to be able to select a view that any of his/her NCs would have, and print-out tallysheets and maps on a neighborhood-by-neighborhood basis.

Welcome Committee (WC)

*    New signups via the public website are in a "queue", waiting to be assigned to NCs.  
-    Either automatically from the database, or entering the new FD's address by hand, the WC needs to be able to map the new FD's location and easily find which NC's are nearer to that FD's location.  Since the NCs locations will be stored in the MySQL database, the map application needs to tap into those records.
    The WC then has a clear process for sending a Welcome Message to the new Food Donor, contacting the prospective NC, recording notes that only certain Rolls have access to (Welcome Committee, District Volunteer Coordinator, Data Manager, Webmaster, Admin).   
	
	ONLY NEEDS TO PROCESS NEW MEMBERS

???Volunteer Coordinator (VC)

    In the system, the VC is very similar to the WC and DM in terms of the level of access they need.  The difference is that if he/she ends up not covering the WC responsiblities, then he/she shouldn't need access to the new FD queue.  So, the purpose of having a VC is to provide that person with access to the full database and record notes pertinent to supporting any of the volunteers in the system.  The key is that this person has a lot of flexibility.

???Data Manager (DM)

    This person needs to be able to efficiently enter the FD history and notes from tallysheets.  Ideally, he/she can enter this donation history from the tallysheet in the pickup order that was used by the NC.   
    The second job is to periodically review all the new user records that have been entered or changed recently, and correct any obvious formatting errors or typos.  They should be able to filter user records by date.
	
	see FDs by date range (when was it entered)
	NEED TIME/DATE STAMP FOR WHEN DONOR ENTERED
	REVIEWS CHANGES TO MEMBER DATA, RE-GEOCODES WHEN ADDRESSES CHANGE, ETC.

???Admin

    Logins in to make changes to the website.  This role doesn't necessarily need to pertain to any functionality in the "Database Application," other than they need to be able to change  records in all the ways that all other roles are able to do.






-------------------------------------------------------------------------------------
STREET NAMES
	In order to present a standardized way of spelling street names (is it '1st street' or 'first st.' or ...), we create a drop-down box of street names.
	You need to get a list of street names from the county(?). Load the CSV file into the database as table 'streetnames' with fields:
	PRE_DIRECTION, STREET_NAME, STREET_TYPE_CODE, POST_DIRECTION, FullStreetName, CITY, STATE, ZIPCODE
	