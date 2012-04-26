<?
C - create
    Key[insert] = utilDb->insert()
	
	utilDb->insert($tbl, $array);

R - read
	Key[select] = utilDb->select()
	
	// Selecting everything
	utilDb->select($tbl); 
	
	// Note
	// - Second field(arrayfield), if not array then it is asterisk
	
	// Selecting specific fields
	// Second parameter for array fields
	utilDb->select($tbl, $arrayfield);

	// Selecting data with 'where' expression
	// 'where' keyword is auto generated
	utilDb->select($tbl, $arrayfield, $stringWhere, 'row/s'); 
	
	// Count rows
	utilDb->selectCount($tbl, $stringWhere); 
		
U - update
	Key[update] = utilDb->update()

	// $arrayFields should be assoc array
	utilDb->update($tbl, $arrayFields, $arrayWhere)
	
D - delete
	Key[delete] = utilDb->delete()
	
	// Delete all
	utilDb->deleteAll($tbl)
	
	// Delete where
	utilDb->deleteWhere($tbl, $arrayWhere)

CRUD - all functionality
	Key[sql] = utilDb->sql($stringstatement)


Method chaining
 - utilDb->select()->debug()
 - utilDb->select()->execute()
