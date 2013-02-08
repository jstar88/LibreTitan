package models;

import java.util.*;
import play.db.ebean.*;
import play.data.validation.Constraints.*;
import javax.persistence.*;
import play.data.format.*;

@Entity
@Table(name = "roles")
public class Role extends Model {

	@Id
	public Integer id;

	@ManyToMany(cascade = CascadeType.ALL)
	public List<User> users;

	// ---------- override functions ------------
	public boolean equals(Object obj) {
		if (obj instanceof Role) {
			Role e = (Role) obj;
			return e.id == id;
		} else
			return super.equals(obj);
	}

	// ---------- static functions ------------
	public static Model.Finder<String, Role> find = new Model.Finder(
			String.class, Role.class);

	public static Role findById(Integer id) {
		return find.where().eq("id", id).findUnique();
	}

}