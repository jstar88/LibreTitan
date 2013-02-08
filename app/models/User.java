package models;

import java.util.*;
import play.db.ebean.*;
import play.data.validation.Constraints.*;
import javax.persistence.*;
import play.data.format.*;

@Entity
@Table(name = "users")
public class User extends Model {

	@Id
	public Long id;

	@Required
	public String email;

	@Required
	public String name;

	@Required
	public String password;

	@OneToOne(cascade = CascadeType.ALL)
	public Profile profile;

	@OneToOne(cascade = CascadeType.ALL)
	public CelestialObject homePlanet;

	@OneToMany(cascade = CascadeType.ALL)
	public List<CelestialObject> planets;

	@ManyToMany(cascade = CascadeType.ALL)
	public List<Role> roles;

	public User(String name, String email, String password, Profile profile) {
		this.name = name;
		this.email = email;
		this.password = password;
		this.profile = profile;
	}

	public static class Profile {

		public String country;
		public String address;
		public Integer age;

		public Profile(String country, String address, Integer age) {
			this.country = country;
			this.address = address;
			this.age = age;
		}
	}

	/**
	 * Ad a role to current user.
	 */
	public void addRole(Integer id) throws ClassNotFoundException{
		Role role = Role.findById(id);
		if (role == null)
			throw new ClassNotFoundException("Role with id:" + id
					+ " not found.");
		roles.add(role);
	}

	/**
	 * Determinate if this user have the role with id as argument
	 */
	public boolean haveRole(Integer id) {
		Role tmp = new Role();
		tmp.id = id;
		return roles.contains(tmp);

	}

	/**
	 * Determinate if this user have the role as argument
	 */
	public boolean haveRole(Role role) {
		return roles.contains(role);

	}

	// ---------- override functions ------------
	public String toString() {
		return "User(" + email + ")";
	}

	public boolean equals(Object obj) {
		if (obj instanceof User) {
			User e = (User) obj;
			return e.id == id;
		} else
			return super.equals(obj);
	}

	// ---------- static functions ------------
	public static Model.Finder<String, User> find = new Model.Finder(
			String.class, User.class);

	/**
	 * Retrieve all users.
	 */
	public static List<User> findAll() {
		return find.all();
	}

	/**
	 * Retrieve a User from id.
	 */
	public static User findById(Long id) {
		return find.where().eq("id", id).findUnique();
	}

	/**
	 * Authenticate a User.
	 */
	public static User authenticate(String email, String password) {
		return find.where().eq("email", email).eq("password", password)
				.findUnique();
	}

}