package models;

import java.util.*;
import play.db.ebean.*;
import play.data.validation.Constraints.*;
import javax.persistence.*;
import play.data.format.*;


@Entity
@Table(name="celestial_objects")
public class CelestialObject extends Model {
	
	@Id
	public Long id;

	@ManyToOne(cascade = { CascadeType.ALL } )
	public User user;

}